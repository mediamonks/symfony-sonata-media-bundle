<?php

namespace MediaMonks\SonataMediaBundle\Handler;

use MediaMonks\SonataMediaBundle\Model\MediaInterface;

interface ParameterHandlerInterface
{
    public function getRouteParameters(MediaInterface $media, ParameterBagInterface $parameterBag);

    public function getPayload(MediaInterface $media, ParameterBagInterface $parameterBag);
}
