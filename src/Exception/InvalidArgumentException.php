<?php

namespace MediaMonks\SonataMediaBundle\Exception;

class InvalidArgumentException extends \InvalidArgumentException
{
    public static function from(string $class, string $function, string $expected, string $got): InvalidArgumentException
    {
        return new static(sprintf('Class %s::%s, expected %s got %s', $class, $function, $expected, $got));
    }
}
