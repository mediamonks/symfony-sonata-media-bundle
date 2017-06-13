<?php

namespace MediaMonks\SonataMediaBundle\Handler;

use MediaMonks\SonataMediaBundle\Model\MediaInterface;

interface ParameterBagInterface
{
    public function toArray(MediaInterface $media);
}
