<?php

namespace MediaMonks\SonataMediaBundle\Handler;

class ParameterBag implements ParameterBagInterface
{
    /**
     * @var int
     */
    protected $id;

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
     * @param int $id
     * @param int $width
     * @param int $height
     * @param array $extra
     */
    public function __construct($id, $width, $height, array $extra = [])
    {
        $this->id = $id;
        $this->width = $width;
        $this->height = $height;
        $this->extra = $extra;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return int
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * @return int
     */
    public function getHeight()
    {
        return $this->height;
    }

    /**
     * @return array
     */
    public function getExtra()
    {
        return $this->extra;
    }

    /**
     * @param array $defaults
     */
    public function setDefaults(array $defaults)
    {
        $this->extra = array_merge($defaults, $this->extra);
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return array_merge($this->getExtra(), [
            'id' => $this->getId(),
            'width' => $this->getWidth(),
            'height' => $this->getHeight(),
        ]);
    }
}
