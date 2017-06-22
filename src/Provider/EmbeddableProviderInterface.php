<?php

namespace MediaMonks\SonataMediaBundle\Provider;

interface EmbeddableProviderInterface extends ProviderInterface
{
    public function getEmbedTemplate();
}
