<?php

namespace MediaMonks\SonataMediaBundle\ParameterBag;

use MediaMonks\SonataMediaBundle\Model\MediaInterface;

interface ParameterBagInterface
{
    public function removeExtra($key);

    public function toArray(MediaInterface $media);
}
