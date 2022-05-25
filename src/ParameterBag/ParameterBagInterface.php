<?php

namespace MediaMonks\SonataMediaBundle\ParameterBag;

use MediaMonks\SonataMediaBundle\Model\MediaInterface;

interface ParameterBagInterface
{
    public function removeExtra(string $key): void;

    public function toArray(MediaInterface $media): array;
}
