<?php

namespace MediaMonks\SonataMediaBundle\Provider;

interface EmbeddableProviderInterface
{
    public function getEmbedTemplate(): string;
}
