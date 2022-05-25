<?php

namespace MediaMonks\SonataMediaBundle\Client;

interface HttpClientInterface
{
    /**
     * Performs a GET request and returns the content as a string.
     *
     * @param string $url
     *
     * @return string
     */
    public function get(string $url): string;

    /**
     * Attempts to verify if the request is valid/exists.
     *
     * @param string $url
     *
     * @return bool
     */
    public function exists(string $url): bool;
}
