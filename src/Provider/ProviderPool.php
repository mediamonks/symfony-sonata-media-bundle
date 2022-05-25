<?php

namespace MediaMonks\SonataMediaBundle\Provider;

use InvalidArgumentException;
use MediaMonks\SonataMediaBundle\Model\MediaInterface;

class ProviderPool
{
    protected array $providers = [];

    /**
     * @param $name
     *
     * @return ProviderInterface
     */
    public function getProvider($name): ProviderInterface
    {
        if (!isset($this->providers[$name])) {
            throw new InvalidArgumentException(
                sprintf('Provider with name "%s" does not exist', $name)
            );
        }

        return $this->providers[$name];
    }

    /**
     * @param ProviderInterface $provider
     */
    public function addProvider(ProviderInterface $provider): void
    {
        $this->providers[$provider->getName()] = $provider;
    }

    /**
     * @param ProviderInterface[] $providers
     */
    public function addProviders(array $providers): void
    {
        foreach ($providers as $provider) {
            $this->addProvider($provider);
        }
    }

    /**
     * @param array $providers
     *
     * @return void
     */
    public function setProviders(array $providers): void
    {
        $this->providers = [];
        $this->addProviders($providers);
    }

    /**
     * @return ProviderInterface[]
     */
    public function getProviders(): array
    {
        return $this->providers;
    }

    /**
     * @param array $types
     *
     * @return ProviderInterface[]
     */
    public function getProvidersByTypes(array $types): array
    {
        $providers = [];
        foreach ($this->getProviders() as $provider) {
            if (in_array($provider->getType(), $types)) {
                $providers[$provider->getName()] = $provider;
            }
        }

        return $providers;
    }

    /**
     * @param MediaInterface $media
     *
     * @return ProviderInterface
     */
    public function getByMedia(MediaInterface $media): ProviderInterface
    {
        return $this->getProvider($media->getProvider());
    }
}
