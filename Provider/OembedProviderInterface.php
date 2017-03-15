<?php

namespace MediaMonks\SonataMediaBundle\Provider;

interface OembedProviderInterface extends ProviderInterface
{
    public function getOembedUrl($id);

    public function parseProviderReference($value);
}
