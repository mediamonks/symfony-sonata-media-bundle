<?php

namespace MediaMonks\SonataMediaBundle\Handler;

interface ParameterHandlerInterface
{
    public function getRouteParameters(ParameterBag $parameterBag);

    public function getPayload($id, $width, $height, array $extra);
}
