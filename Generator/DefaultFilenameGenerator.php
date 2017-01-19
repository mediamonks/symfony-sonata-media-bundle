<?php

namespace MediaMonks\SonataMediaBundle\Generator;

use MediaMonks\SonataMediaBundle\Model\MediaInterface;

class DefaultFilenameGenerator implements FilenameGeneratorInterface
{
    const FORMAT_JPG = 'jpg';

    /**
     * @param MediaInterface $media
     * @param array $parameters
     * @return string
     */
    public function generate(MediaInterface $media, array $parameters)
    {
        $parametersFlat = [];
        foreach ($parameters as $k => $v) {
            $parametersFlat[] = $k.$v;
        }

        return pathinfo($media->getImage(), PATHINFO_FILENAME).
            '/'.implode('_', $parametersFlat).
            '.'.$this->getFormat($parameters);
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
