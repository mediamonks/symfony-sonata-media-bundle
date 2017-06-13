<?php

namespace MediaMonks\SonataMediaBundle\Handler;

use MediaMonks\SonataMediaBundle\Model\MediaInterface;

class ImageParameterBag extends AbstractMediaParameterBag
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
     * @param int $width
     * @param int $height
     * @param array $extra
     */
    public function __construct($width, $height, array $extra = [])
    {
        $this->width = $width;
        $this->height = $height;

        parent::__construct($extra);
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
     * @param MediaInterface $media
     * @return array
     */
    public function toArray(MediaInterface $media)
    {
        return array_merge(parent::toArray($media), [
            'width' => $this->getWidth(),
            'height' => $this->getHeight(),
        ]);
    }
}
