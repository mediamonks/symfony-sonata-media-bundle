<?php

namespace MediaMonks\SonataMediaBundle\Exception;

use Throwable;

class InvalidProviderUrlException extends InvalidQueryParameterException
{
    public function __construct($provider, $code = 0, Throwable $previous = null)
    {
        parent::__construct(sprintf('The supplied URL does not look like a %s URL', $provider), $code, $previous);
    }
}
