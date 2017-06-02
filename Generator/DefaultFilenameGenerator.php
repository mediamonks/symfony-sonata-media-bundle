<?php

namespace MediaMonks\SonataMediaBundle\Generator;

use MediaMonks\SonataMediaBundle\Handler\ParameterBag;
use MediaMonks\SonataMediaBundle\Model\MediaInterface;

class DefaultFilenameGenerator implements FilenameGeneratorInterface
{
    const FORMAT_JPG = 'jpg';

    /**
     * @param MediaInterface $media
     * @param ParameterBag $parameterBag
     * @return string
     */
    public function generate(MediaInterface $media, ParameterBag $parameterBag)
    {
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
     * @return string
     */
    private function getFormat(array $parameters)
    {
        if (isset($parameters['fm'])) {
            return $parameters['fm'];
        }

        return self::FORMAT_JPG;
    }
}
