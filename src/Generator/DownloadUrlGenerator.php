<?php

namespace MediaMonks\SonataMediaBundle\Generator;

use MediaMonks\SonataMediaBundle\Model\MediaInterface;
use MediaMonks\SonataMediaBundle\ParameterBag\MediaParameterBag;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface as SymfonyUrlGeneratorInterface;

class DownloadUrlGenerator extends AbstractUrlGenerator
{
    /**
     * @param MediaInterface $media
     * @param array $extra
     * @param string|null $routeName
     * @param int $referenceType
     *
     * @return string
     */
    public function generateDownloadUrl(
        MediaInterface $media,
        array $extra = [],
        ?string $routeName = null,
        int $referenceType = SymfonyUrlGeneratorInterface::ABSOLUTE_PATH
    ): string
    {
        return $this->generate($media, new MediaParameterBag($extra), $routeName, $referenceType);
    }
}
