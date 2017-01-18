<?php

namespace MediaMonks\SonataMediaBundle\Model;

use MediaMonks\SonataMediaBundle\Helper\Parameter;

trait MediaTrait
{
    /**
     * @return array
     */
    public function getDefaultImageOptions()
    {
        $fit = 'crop-center';
        if (!$this instanceof MediaInterface) {
            if (!is_null($this->getPointOfInterest())) {
                $fit = 'crop-'.$this->getPointOfInterest();
            }
        }

        return [
            'fit' => $fit,
        ];
    }

    /**
     * @return array
     */
    public function getDefaultUrlParameters()
    {
        if (!$this instanceof MediaInterface) {
            return [];
        }

        return [
            Parameter::PARAMETER_ID      => $this->getId(),
            Parameter::PARAMETER_VERSION => $this->getUpdatedAt()->getTimestamp(),
        ];
    }
}
