<?php

namespace MediaMonks\SonataMediaBundle\Model;

abstract class AbstractMedia implements MediaInterface
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var string
     */
    protected $title;

    /**
     * @var string
     */
    protected $description;

    /**
     * @var string
     */
    protected $provider;

    /**
     * @var string
     */
    protected $type;

    /**
     * @var string
     */
    protected $providerReference;

    /**
     * @var array
     */
    protected $providerMetaData = [];

    /**
     * @var string
     */
    protected $image;

    /**
     * @var array
     */
    protected $imageMetaData = [];

    /**
     * @var string
     */
    protected $focalPoint;

    /**
     * @var string
     */
    protected $copyright;

    /**
     * @var string
     */
    protected $authorName;

    /**
     * @var \DateTime
     */
    protected $createdAt;

    /**
     * @var \DateTime
     */
    protected $updatedAt;

    /**
     * @var string
     */
    protected $binaryContent;

    /**
     * @var string
     */
    protected $imageContent;

    public function __construct()
    {
        $this->createdAt = new \DateTime;
        $this->updatedAt = new \DateTime;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     * @return AbstractMedia
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param mixed $title
     * @return AbstractMedia
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     * @return AbstractMedia
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return string
     */
    public function getProvider()
    {
        return $this->provider;
    }

    /**
     * @param string $provider
     * @return AbstractMedia
     */
    public function setProvider($provider)
    {
        $this->provider = $provider;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param mixed $type
     * @return MediaInterface
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getProviderReference()
    {
        return $this->providerReference;
    }

    /**
     * @param mixed $providerReference
     */
    public function setProviderReference($providerReference)
    {
        $this->providerReference = $providerReference;
    }

    /**
     * @return array
     */
    public function getProviderMetaData()
    {
        return $this->providerMetaData;
    }

    /**
     * @param array $providerMetaData
     */
    public function setProviderMetaData(array $providerMetaData)
    {
        $this->providerMetaData = $providerMetaData;
    }

    /**
     * @return mixed
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * @param mixed $image
     * @return MediaInterface
     */
    public function setImage($image)
    {
        $this->image = $image;

        return $this;
    }

    /**
     * @return array
     */
    public function getImageMetaData()
    {
        return $this->imageMetaData;
    }

    /**
     * @param array $imageMetaData
     * @return MediaInterface
     */
    public function setImageMetaData(array $imageMetaData)
    {
        $this->imageMetaData = $imageMetaData;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getFocalPoint()
    {
        if (empty($this->focalPoint)) {
            return '50-50';
        }

        return $this->focalPoint;
    }

    /**
     * @param mixed $focalPoint
     * @return MediaInterface
     */
    public function setFocalPoint($focalPoint)
    {
        $this->focalPoint = $focalPoint;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getCopyright()
    {
        return $this->copyright;
    }

    /**
     * @param mixed $copyright
     * @return AbstractMedia
     */
    public function setCopyright($copyright)
    {
        $this->copyright = $copyright;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getAuthorName()
    {
        return $this->authorName;
    }

    /**
     * @param mixed $authorName
     * @return MediaInterface
     */
    public function setAuthorName($authorName)
    {
        $this->authorName = $authorName;

        return $this;
    }

    /**
     * @return string
     */
    public function getBinaryContent()
    {
        return $this->binaryContent;
    }

    /**
     * @param string $binaryContent
     * @return MediaInterface
     */
    public function setBinaryContent($binaryContent)
    {
        $this->binaryContent = $binaryContent;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getSlug()
    {
        return $this->getId();
    }

    /**
     * @return mixed
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param mixed $createdAt
     * @return MediaInterface
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * @param \DateTime $updatedAt
     * @return MediaInterface
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * @return string
     */
    public function getImageContent()
    {
        return $this->imageContent;
    }

    /**
     * @param string $imageContent
     * @return MediaInterface
     */
    public function setImageContent($imageContent)
    {
        $this->imageContent = $imageContent;

        return $this;
    }

    /**
     * @return mixed
     */
    public function __toString()
    {
        return sprintf('%s (%s)', $this->getTitle(), $this->getType());
    }
}
