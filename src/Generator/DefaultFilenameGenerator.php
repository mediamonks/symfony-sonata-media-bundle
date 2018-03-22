<?php

namespace MediaMonks\SonataMediaBundle\Generator;

use MediaMonks\SonataMediaBundle\ParameterBag\ImageParameterBag;
use MediaMonks\SonataMediaBundle\Model\MediaInterface;

class DefaultFilenameGenerator implements FilenameGeneratorInterface
{

    const FORMAT_JPG = 'jpg';

    /**
     * @param MediaInterface $media
     * @param ImageParameterBag $parameterBag
     *
     * @return string
     */
    public function generate(
        MediaInterface $media,
        ImageParameterBag $parameterBag
    ): string {
        $parametersFlat = [];
        foreach ($parameterBag->toArray($media) as $k => $v) {
            $parametersFlat[] = $k.'_'.$v;
        }

        return pathinfo($media->getImage(), PATHINFO_FILENAME).
            '/'.implode('-', $parametersFlat).
            '.'.$this->getFormat($parameterBag->getExtra());
    }

    /**
     * @param array $parameters
     *
     * @return string
     */
    private function getFormat(array $parameters): string
    {
        if (isset($parameters['fm'])) {
            return $parameters['fm'];
        }

        return self::FORMAT_JPG;
    }
}
