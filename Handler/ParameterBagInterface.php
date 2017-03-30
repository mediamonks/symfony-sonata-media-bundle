<?php

namespace MediaMonks\SonataMediaBundle\Handler;

interface ParameterBagInterface
{
    public function getId();

    public function getWidth();

    public function getHeight();

    public function getExtra();
}
