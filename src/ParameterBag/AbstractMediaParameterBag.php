<?php

namespace MediaMonks\SonataMediaBundle\ParameterBag;

use MediaMonks\SonataMediaBundle\Model\MediaInterface;

abstract class AbstractMediaParameterBag implements ParameterBagInterface
{
    protected array $extra = [];

    /**
     * @param array $extra
     */
    public function __construct(array $extra = [])
    {
        $this->extra = $extra;
    }

    /**
     * @return array
     */
    public function getExtra(): array
    {
        return $this->extra;
    }

    /**
     * @param array $extra
     */
    public function setExtra(array $extra): void
    {
        $this->extra = $extra;
    }

    /**
     * @param string $key
     * @param mixed $value
     */
    public function addExtra(string $key, $value): void
    {
        $this->extra[$key] = $value;
    }

    /**
     * @param string $key
     *
     * @return bool
     */
    public function hasExtra(string $key): bool
    {
        return isset($this->extra[$key]);
    }

    /**
     * @param string $key
     */
    public function removeExtra(string $key): void
    {
        unset($this->extra[$key]);
    }

    /**
     * @param array $defaults
     */
    public function setDefaults(array $defaults)
    {
        $this->extra = array_merge($defaults, $this->extra);
    }

    /**
     * @param MediaInterface $media
     *
     * @return array
     */
    public function toArray(MediaInterface $media): array
    {
        return array_merge($this->getExtra(), [
            'id' => $media->getId()
        ]);
    }
}
