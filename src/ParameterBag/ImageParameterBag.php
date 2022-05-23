<?php

namespace MediaMonks\SonataMediaBundle\ParameterBag;

use MediaMonks\SonataMediaBundle\Model\MediaInterface;

class ImageParameterBag extends MediaParameterBag
{
    protected ?int $width;
    protected ?int $height;

    /**
     * @param int|null $width
     * @param int|null $height
     * @param array $extra
     */
    public function __construct(?int $width, ?int $height, array $extra = [])
    {
        $this->width = $width;
        $this->height = $height;

        parent::__construct($extra);
    }

    /**
     * @return int|null
     */
    public function getWidth(): ?int
    {
        return $this->width;
    }

    /**
     * @param int|null $width
     */
    public function setWidth(?int $width): void
    {
        $this->width = $width;
    }

    /**
     * @return int|null
     */
    public function getHeight(): ?int
    {
        return $this->height;
    }

    /**
     * @param int|null $height
     */
    public function setHeight(?int $height): void
    {
        $this->height = $height;
    }

    /**
     * @param MediaInterface $media
     *
     * @return array
     */
    public function toArray(MediaInterface $media): array
    {
        return array_merge(parent::toArray($media), [
            'width' => $this->getWidth(),
            'height' => $this->getHeight(),
        ]);
    }
}
