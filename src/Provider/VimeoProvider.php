<?php

namespace MediaMonks\SonataMediaBundle\Provider;

use MediaMonks\SonataMediaBundle\Exception\InvalidProviderUrlException;
use Throwable;

class VimeoProvider extends AbstractOembedProvider implements ProviderInterface, EmbeddableProviderInterface
{
    const URL_OEMBED = 'https://vimeo.com/api/oembed.json?url=http://vimeo.com/%s';

    /**
     * @param string $value
     *
     * @return string
     * @throws Throwable
     */
    public function parseProviderReference(string $value): string
    {
        if (strpos($value, 'vimeo.com')) {
            $urlParts = explode('/', parse_url($value, PHP_URL_PATH));
            foreach ($urlParts as $urlPart) {
                if (ctype_digit($urlPart)) {
                    return $urlPart;
                }
            }

            throw new InvalidProviderUrlException('Vimeo');
        }

        return $value;
    }

    /**
     * @param string $id
     *
     * @return string
     */
    public function getOembedUrl(string $id): string
    {
        return sprintf(self::URL_OEMBED, $id);
    }

    /**
     * @return string
     */
    public function getIcon(): string
    {
        return 'fa fa-vimeo';
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return 'vimeo';
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return AbstractProvider::TYPE_VIDEO;
    }
}
