<?php

namespace MediaMonks\SonataMediaBundle\Provider;

interface OembedProviderInterface extends ProviderInterface
{
    public function getOembedUrl($id): string;

    public function parseProviderReference($value): string;
}
