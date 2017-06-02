<?php

namespace MediaMonks\SonataMediaBundle\Handler;

use MediaMonks\SonataMediaBundle\Model\MediaInterface;

class ParameterBag implements ParameterBagInterface
{
    /**
     * @var int
     */
    protected $width;

    /**
     * @var int
     */
    protected $height;

    /**
     * @var array
     */
    protected $extra = [];

    /**
     * @param int $width
     * @param int $height
     * @param array $extra
     */
    public function __construct($width, $height, array $extra = [])
    {
        $this->width = $width;
        $this->height = $height;
        $this->extra = $extra;
    }

    /**
     * @return int
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * @param int $width
     */
    public function setWidth($width)
    {
        $this->width = $width;
    }

    /**
     * @return int
     */
    public function getHeight()
    {
        return $this->height;
    }

    /**
     * @param int $height
     */
    public function setHeight($height)
    {
        $this->height = $height;
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
            'id' => $media->getId(),
            'width' => $this->getWidth(),
            'height' => $this->getHeight(),
        ]);
    }
}
