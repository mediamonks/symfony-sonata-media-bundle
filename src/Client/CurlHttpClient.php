<?php

namespace MediaMonks\SonataMediaBundle\Client;

use Symfony\Component\HttpFoundation\Response;

class CurlHttpClient implements HttpClientInterface
{
    private int $connectTimeout = 5;
    private int $timeout = 5;

    /** @inheritDoc */
    public function get(string $url): string
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_URL, $url);

        $data = curl_exec($ch);
        curl_close($ch);

        return $data;
    }

    /** @inheritDoc */
    public function exists(string $url): bool
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_NOBODY, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $this->connectTimeout);
        curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeout);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'HEAD');

        curl_exec($ch);
        $info = curl_getinfo($ch);
        curl_close($ch);

        return $info['http_code'] === Response::HTTP_OK;
    }
}
