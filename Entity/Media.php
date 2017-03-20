<?php

namespace MediaMonks\SonataMediaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use MediaMonks\SonataMediaBundle\Model\MediaInterface;

/**
 * @ORM\Entity(repositoryClass="MediaRepository")
 * @ORM\Table(name="media")
 */
class Media implements MediaInterface
{
    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $title;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(type="string")
     */
    private $provider;

    /**
     * @ORM\Column(type="string")
     */
    private $type;

    /**
     * @ORM\Column(type="string")
     */
    private $providerReference;

    /**
     * @ORM\Column(type="json_array")
     */
    private $providerMetaData = [];

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $image;

    /**
     * @ORM\Column(type="json_array")
     */
    private $imageMetaData = [];

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $focalPoint;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $copyright;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $authorName;

    /**
     * @var \DateTime
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @var \DateTime
     * @ORM\Column(type="datetime")
     */
    private $updatedAt;

    /**
     * @var string
     */
    private $binaryContent;

    /**
     * @var string
     */
    private $imageContent;

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
     */
    public function setId($id)
    {
        $this->id = $id;
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
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param mixed $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return mixed
     */
    public function getProvider()
    {
        return $this->provider;
    }

    /**
     * @param mixed $provider
     * @return Media
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
     * @return Media
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
     * @return Media
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
     * @return Media
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
        return $this->focalPoint;
    }

    /**
     * @param mixed $focalPoint
     * @return Media
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
     */
    public function setCopyright($copyright)
    {
        $this->copyright = $copyright;
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
     * @return Media
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
     * @return Media
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
     * @return Media
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
     * @return Media
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
     * @return Media
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
