<?php

namespace MediaMonks\SonataMediaBundle\Provider;

use MediaMonks\SonataMediaBundle\Model\MediaInterface;

class ProviderPool
{
    /**
     * @var array
     */
    protected $providers = [];

    /**
     * @param $name
     * @return AbstractProvider
     */
    public function getProvider($name)
    {
        if (!isset($this->providers[$name])) {
            throw new \InvalidArgumentException(
                sprintf('Provider with name "%s" does not exist', $name)
            );
        }

        return $this->providers[$name];
    }

    /**
     * @param ProviderInterface $provider
     */
    public function addProvider(ProviderInterface $provider)
    {
        $this->providers[$provider->getName()] = $provider;
    }

    /**
     * @param ProviderInterface[] $providers
     */
    public function addProviders(array $providers)
    {
        foreach ($providers as $provider) {
            $this->addProvider($provider);
        }
    }

    /**
     * @param $providers
     */
    public function setProviders(array $providers)
    {
        $this->providers = [];
        $this->addProviders($providers);
    }

    /**
     * @return ProviderInterface[]
     */
    public function getProviders()
    {
        return $this->providers;
    }

    /**
     * @param array $types
     * @return ProviderInterface[]
     */
    public function getProvidersByTypes(array $types)
    {
        $providers = [];
        foreach ($this->getProviders() as $provider) {
            if (in_array($provider->getType(), $types)) {
                $providers[] = $provider;
            }
        }

        return $providers;
    }

    /**
     * @param MediaInterface $media
     * @return AbstractProvider
     */
    public function getByMedia(MediaInterface $media)
    {
        return $this->getProvider($media->getProvider());
    }
}
