<?php

namespace MediaMonks\SonataMediaBundle\Handler;

use MediaMonks\SonataMediaBundle\Model\MediaInterface;
use Symfony\Component\HttpFoundation\Request;

interface ParameterHandlerInterface
{
    public function getQueryString(MediaInterface $media, array $parameters);

    public function getPayload(MediaInterface $media, Request $request);
}
