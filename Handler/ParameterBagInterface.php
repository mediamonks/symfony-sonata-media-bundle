<?php

namespace MediaMonks\SonataMediaBundle\Handler;

interface ParameterBagInterface
{
    public function getWidth();

    public function getHeight();

    public function getExtra();
}
