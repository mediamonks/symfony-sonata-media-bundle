<?php

namespace MediaMonks\SonataMediaBundle\Generator;

use MediaMonks\SonataMediaBundle\Handler\ParameterBag;

interface FilenameGeneratorInterface
{
    public function generate(ParameterBag $parameterBag);
}
