<?php

namespace MediaMonks\SonataMediaBundle\Provider;

class VimeoProvider extends AbstractOembedProvider implements OembedProviderInterface
{
    const URL_OEMBED = 'https://vimeo.com/api/oembed.json?url=http://vimeo.com/%s';

    /**
     * @param $value
     * @return string
     * @throws \Exception
     */
    public function parseProviderReference($value)
    {
        if (strpos($value, 'vimeo.com')) {
            $urlParts = explode('/', parse_url($value, PHP_URL_PATH));
            foreach ($urlParts as $urlPart) {
                if (ctype_digit($urlPart)) {
                    return $urlPart;
                }
            }

            throw new \Exception('The supplied URL does not look like a Vimeo URL');
        }

        return $value;
    }

    /**
     * @param string $id
     * @return string
     */
    public function getOembedUrl($id)
    {
        return sprintf(self::URL_OEMBED, $id);
    }

    /**
     * @return string
     */
    public function getIcon()
    {
        return 'fa fa-vimeo';
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'vimeo';
    }

    /**
     * @return string
     */
    public function getType()
    {
        return AbstractProvider::TYPE_VIDEO;
    }
}
