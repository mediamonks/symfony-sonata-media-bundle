<?php

namespace MediaMonks\SonataMediaBundle\ParameterBag;

use MediaMonks\SonataMediaBundle\Model\MediaInterface;

abstract class AbstractMediaParameterBag implements ParameterBagInterface
{
    /**
     * @var array
     */
    protected $extra = [];

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
    public function getExtra()
    {
        return $this->extra;
    }

    /**
     * @param array $extra
     */
    public function setExtra($extra)
    {
        $this->extra = $extra;
    }

    /**
     * @param $key
     * @param $value
     */
    public function addExtra($key, $value)
    {
        $this->extra[$key] = $value;
    }

    /**
     * @param $key
     * @return bool
     */
    public function hasExtra($key)
    {
        return isset($this->extra[$key]);
    }

    /**
     * @param $key
     */
    public function removeExtra($key)
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
     * @return array
     */
    public function toArray(MediaInterface $media)
    {
        return array_merge($this->getExtra(), [
            'id' => $media->getId()
        ]);
    }
}
