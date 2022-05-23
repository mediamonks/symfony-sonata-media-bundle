<?php

namespace MediaMonks\SonataMediaBundle\Provider;

interface OembedProviderInterface extends ProviderInterface
{
    public function getOembedUrl(string $id): string;

    public function parseProviderReference(string $value): string;
}
