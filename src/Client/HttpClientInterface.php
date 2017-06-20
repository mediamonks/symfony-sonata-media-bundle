<?php

namespace MediaMonks\SonataMediaBundle\Client;

interface HttpClientInterface
{
    public function getData($url);

    public function exists($url);
}
