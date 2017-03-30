<?php

namespace MediaMonks\SonataMediaBundle\Generator;

use MediaMonks\SonataMediaBundle\Handler\ParameterBag;

class DefaultFilenameGenerator implements FilenameGeneratorInterface
{
    const FORMAT_JPG = 'jpg';

    /**
     * @param ParameterBag $parameterBag
     * @return string
     */
    public function generate(ParameterBag $parameterBag)
    {
        $parametersFlat = [];
        foreach ($parameterBag->toArray() as $k => $v) {
            $parametersFlat[] = $k.'_'.$v;
        }

        return pathinfo($parameterBag->getId(), PATHINFO_FILENAME).
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
