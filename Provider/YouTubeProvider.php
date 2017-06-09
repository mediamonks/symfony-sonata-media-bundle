<?php

namespace MediaMonks\SonataMediaBundle\Provider;

class YouTubeProvider extends AbstractOembedProvider implements ProviderInterface
{
    const URL_OEMBED = 'http://www.youtube.com/oembed?url=http://www.youtube.com/watch?v=%s&format=json';
    const URL_IMAGE_MAX_RES = 'https://i.ytimg.com/vi/%s/maxresdefault.jpg';
    const URL_IMAGE_HQ = 'https://i.ytimg.com/vi/%s/hqdefault.jpg';

    /**
     * @param string $id
     * @return string
     */
    public function getOembedUrl($id)
    {
        return sprintf(self::URL_OEMBED, $id);
    }

    /**
     * @param string $id
     * @return string
     */
    public function getImageUrl($id)
    {
        // try to get max res image (only available for 720P videos)
        $urlMaxRes = sprintf(self::URL_IMAGE_MAX_RES, $id);
        if ($this->getHttpClient()->exists($urlMaxRes)) {
            return $urlMaxRes;
        }

        return sprintf(self::URL_IMAGE_HQ, $id); // this one always exists
    }

    /**
     * @param $value
     * @return string
     * @throws \Exception
     */
    public function parseProviderReference($value)
    {
        if (strpos($value, 'youtube.com')) {
            $url = parse_url($value);
            if (empty($url['query'])) {
                throw new \Exception('The supplied URL does not look like a Youtube URL');
            }
            parse_str($url['query'], $params);
            if (empty($params['v'])) {
                throw new \Exception('The supplied URL does not look like a Youtube URL');
            }

            return $params['v'];
        }

        if (strpos($value, 'youtu.be')) {
            $url = parse_url($value);
            if (empty($url['path']) || empty(trim($url['path'], '/'))) {
                throw new \Exception('The supplied URL does not look like a Youtube URL');
            }
            $id = trim($url['path'], '/');

            return $id;
        }

        return $value;
    }

    /**
     * @return string
     */
    public function getIcon()
    {
        return 'fa fa-youtube-play';
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'youtube';
    }

    /**
     * @return string
     */
    public function getType()
    {
        return AbstractProvider::TYPE_VIDEO;
    }

    /**
     * @return string
     */
    public function getEmbedTemplate()
    {
        return 'MediaMonksSonataMediaBundle:Provider:youtube_embed.html.twig';
    }
}
