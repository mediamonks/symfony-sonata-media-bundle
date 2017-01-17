<?php

namespace MediaMonks\SonataMediaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use MediaMonks\SonataMediaBundle\Model\MediaInterface;
use MediaMonks\SonataMediaBundle\Model\MediaTrait;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="MediaRepository")
 * @ORM\Table(name="media")
 */
class Media implements MediaInterface
{
    use MediaTrait;

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
    private $providerName;

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
     * @ORM\Column(type="string", nullable=true)
     */
    private $pointOfInterest;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $copyright;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $authorName;

    /**
     * @ORM\Column(type="json_array", nullable=true)
     */
    private $tags;

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

    public function __construct()
    {
        $this->tags = [];
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
    public function getProviderName()
    {
        return $this->providerName;
    }

    /**
     * @param mixed $providerName
     * @return Media
     */
    public function setProviderName($providerName)
    {
        $this->providerName = $providerName;
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
     * @return mixed
     */
    public function getProviderMetaData()
    {
        return $this->providerMetaData;
    }

    /**
     * @param mixed $providerMetaData
     */
    public function setProviderMetaData($providerMetaData)
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
     * @return mixed
     */
    public function getPointOfInterest()
    {
        return $this->pointOfInterest;
    }

    /**
     * @param mixed $pointOfInterest
     * @return Media
     */
    public function setPointOfInterest($pointOfInterest)
    {
        $this->pointOfInterest = $pointOfInterest;
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
     * @return string
     */
    public function getMediaType()
    {
       $provider = explode('.', $this->getProviderName());

        return $provider[3];
    }

    /**
     * @return mixed
     */
    public function getTags()
    {
        return $this->tags;
    }

    /**
     * @param mixed $tags
     * @return Media
     */
    public function setTags($tags)
    {
        $this->tags = $tags;

        return $this;
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
}
