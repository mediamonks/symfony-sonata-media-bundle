<?php

namespace MediaMonks\SonataMediaBundle\Model;

use DateTimeImmutable;
use DateTimeInterface;
use InvalidArgumentException;
use MediaMonks\SonataMediaBundle\Provider\AbstractProvider;
use Symfony\Component\HttpFoundation\File\UploadedFile;

abstract class AbstractMedia implements MediaInterface
{
    protected ?int $id = null;
    protected ?string $title = null;
    protected ?string $description = null;
    protected ?string $provider = null;
    protected ?string $type = null;
    protected ?string $providerReference = null;
    protected array $providerMetaData = [];
    protected ?string $image = null;
    protected array $imageMetaData = [];
    protected ?string $focalPoint = null;
    protected ?string $copyright = null;
    protected ?string $authorName = null;
    protected DateTimeInterface $createdAt;
    protected DateTimeInterface $updatedAt;

    /** @var UploadedFile|string|null */
    protected $binaryContent;
    /** @var UploadedFile|string|null */
    protected $imageContent;

    public function __construct()
    {
        $this->createdAt = new DateTimeImmutable();
        $this->updatedAt = new DateTimeImmutable();
    }

    /**
     * @return string
     */
    public function getFocalPoint(): string
    {
        if (empty($this->focalPoint)) {
            return '50-50';
        }

        return $this->focalPoint;
    }

    /**
     * @param string|null $focalPoint
     *
     * @return AbstractMedia
     */
    public function setFocalPoint(?string $focalPoint): AbstractMedia
    {
        $this->focalPoint = $focalPoint;

        return $this;
    }

    /**
     * @return string
     */
    public function getSlug(): string
    {
        return (string)$this->getId();
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param int $id
     *
     * @return AbstractMedia
     */
    public function setId(int $id): AbstractMedia
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * @param string|null $title
     *
     * @return AbstractMedia
     */
    public function setTitle(?string $title): AbstractMedia
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param string|null $description
     *
     * @return AbstractMedia
     */
    public function setDescription(?string $description): AbstractMedia
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getProvider(): ?string
    {
        return $this->provider;
    }

    /**
     * @param string $provider
     *
     * @return AbstractMedia
     */
    public function setProvider(string $provider): AbstractMedia
    {
        $this->provider = $provider;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getType(): ?string
    {
        return $this->type;
    }

    /**
     * @param string $type
     *
     * @return AbstractMedia
     */
    public function setType(string $type): AbstractMedia
    {
        if (!in_array($type, static::getValidTypes())) {
            throw new InvalidArgumentException(sprintf(
                'Media type "%s" is invalid. Please provide one of the following types: [%s]',
                $type,
                implode(', ', static::getValidTypes())
            ));
        }
        $this->type = $type;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getProviderReference(): ?string
    {
        return $this->providerReference;
    }

    /**
     * @param string $providerReference
     *
     * @return AbstractMedia
     */
    public function setProviderReference(string $providerReference): AbstractMedia
    {
        $this->providerReference = $providerReference;

        return $this;
    }

    /**
     * @return array
     */
    public function getProviderMetaData(): array
    {
        return $this->providerMetaData;
    }

    /**
     * @param array $providerMetaData
     *
     * @return AbstractMedia
     */
    public function setProviderMetaData(array $providerMetaData): AbstractMedia
    {
        $this->providerMetaData = $providerMetaData;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getImage(): ?string
    {
        return $this->image;
    }

    /**
     * @param string|null $image
     *
     * @return AbstractMedia
     */
    public function setImage(?string $image): AbstractMedia
    {
        $this->image = $image;

        return $this;
    }

    /**
     * @return array
     */
    public function getImageMetaData(): array
    {
        return $this->imageMetaData;
    }

    /**
     * @param array $imageMetaData
     *
     * @return AbstractMedia
     */
    public function setImageMetaData(array $imageMetaData): AbstractMedia
    {
        $this->imageMetaData = $imageMetaData;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getCopyright(): ?string
    {
        return $this->copyright;
    }

    /**
     * @param string|null $copyright
     *
     * @return AbstractMedia
     */
    public function setCopyright(?string $copyright): AbstractMedia
    {
        $this->copyright = $copyright;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getAuthorName(): ?string
    {
        return $this->authorName;
    }

    /**
     * @param string|null $authorName
     *
     * @return AbstractMedia
     */
    public function setAuthorName(?string $authorName): AbstractMedia
    {
        $this->authorName = $authorName;

        return $this;
    }

    /**
     * @return DateTimeInterface
     */
    public function getCreatedAt(): DateTimeInterface
    {
        return $this->createdAt;
    }

    /**
     * @param DateTimeInterface $createdAt
     *
     * @return $this
     */
    public function setCreatedAt(DateTimeInterface $createdAt): AbstractMedia
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @return DateTimeInterface
     */
    public function getUpdatedAt(): DateTimeInterface
    {
        return $this->updatedAt;
    }

    /**
     * @param DateTimeInterface $updatedAt
     *
     * @return $this
     */
    public function setUpdatedAt(DateTimeInterface $updatedAt): AbstractMedia
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * @return UploadedFile|string|null
     */
    public function getBinaryContent()
    {
        return $this->binaryContent;
    }

    /**
     * @param UploadedFile|string $binaryContent
     *
     * @return AbstractMedia
     */
    public function setBinaryContent($binaryContent): AbstractMedia
    {
        $this->binaryContent = $binaryContent;

        return $this;
    }

    /**
     * @return UploadedFile|string|null
     */
    public function getImageContent()
    {
        return $this->imageContent;
    }

    /**
     * @param UploadedFile|string $imageContent
     *
     * @return AbstractMedia
     */
    public function setImageContent($imageContent): AbstractMedia
    {
        $this->imageContent = $imageContent;

        return $this;
    }

    /**
     * Returns the list of supported media types.
     *
     * @return array
     */
    public static function getValidTypes(): array
    {
        return [
            AbstractProvider::TYPE_AUDIO,
            AbstractProvider::TYPE_IMAGE,
            AbstractProvider::TYPE_FILE,
            AbstractProvider::TYPE_VIDEO,
        ];
    }

    public function __toString(): string
    {
        $title = $this->getTitle();
        if (empty($title)) {
            return sprintf('%s (%s)', $this->getId(), $this->getType());
        }

        return sprintf('%s (%s)', $this->getTitle(), $this->getType());
    }
}
