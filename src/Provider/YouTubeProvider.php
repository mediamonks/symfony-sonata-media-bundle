<?php

namespace MediaMonks\SonataMediaBundle\Provider;

use MediaMonks\SonataMediaBundle\Exception\InvalidProviderUrlException;

class YouTubeProvider extends AbstractOembedProvider implements ProviderInterface, EmbeddableProviderInterface
{
    const URL_OEMBED = 'https://www.youtube.com/oembed?url=http://www.youtube.com/watch?v=%s&format=json';
    const URL_IMAGE_MAX_RES = 'https://i.ytimg.com/vi/%s/maxresdefault.jpg';
    const URL_IMAGE_HQ = 'https://i.ytimg.com/vi/%s/hqdefault.jpg';

    /**
     * @param string $id
     * @return string
     */
    public function getOembedUrl($id): string
    {
        return sprintf(self::URL_OEMBED, $id);
    }

    /**
     * @param string $id
     * @return string
     */
    public function getImageUrl($id): string
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
    public function parseProviderReference($value): string
    {
        if (strpos($value, 'youtube.com')) {
            return $this->parseProviderReferenceFromUrl($value);
        }

        if (strpos($value, 'youtu.be')) {
            return $this->parseProviderReferenceFromShortUrl($value);
        }

        return $value;
    }

    /**
     * @param string $url
     * @return mixed
     * @throws InvalidProviderUrlException
     */
    protected function parseProviderReferenceFromUrl($url): string
    {
        $url = parse_url($url);
        if (empty($url['query'])) {
            throw new InvalidProviderUrlException('Youtube');
        }
        parse_str($url['query'], $params);
        if (empty($params['v'])) {
            throw new InvalidProviderUrlException('Youtube');
        }

        return $params['v'];
    }

    /**
     * @param string $url
     * @return string
     * @throws InvalidProviderUrlException
     */
    protected function parseProviderReferenceFromShortUrl($url): string
    {
        $url = parse_url($url);
        if (empty($url['path']) || empty(trim($url['path'], '/'))) {
            throw new InvalidProviderUrlException('Youtube');
        }
        $id = trim($url['path'], '/');

        return $id;
    }

    /**
     * @return string
     */
    public function getIcon(): string
    {
        return 'fa fa-youtube-play';
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return 'youtube';
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return AbstractProvider::TYPE_VIDEO;
    }
}
