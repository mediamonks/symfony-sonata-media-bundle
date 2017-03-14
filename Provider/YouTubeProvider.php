<?php

namespace MediaMonks\SonataMediaBundle\Provider;

use MediaMonks\SonataMediaBundle\Entity\Media;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\CoreBundle\Validator\ErrorElement;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Response;

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
        stream_context_set_default(['http' => ['method' => 'HEAD']]);
        $headers = get_headers($urlMaxRes);
        stream_context_set_default(['http' => ['method' => 'GET']]);
        if ((int)substr($headers[0], 9, 3) === Response::HTTP_OK) {
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
            $id = substr($url['path'], 1);

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
    public function getTitle()
    {
        return 'YouTube Video';
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
