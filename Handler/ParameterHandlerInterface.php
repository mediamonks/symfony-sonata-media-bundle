<?php

namespace MediaMonks\SonataMediaBundle\Handler;

use MediaMonks\SonataMediaBundle\Model\MediaInterface;

interface ParameterHandlerInterface
{
    public function getRouteParameters(MediaInterface $media, ParameterBag $parameterBag);

    public function getPayload(MediaInterface $media, $width, $height, array $extra);
}
