<?php

namespace MediaMonks\SonataMediaBundle\Entity;

use DateTimeInterface;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use MediaMonks\SonataMediaBundle\Model\AbstractMedia;

#[ORM\Entity]
#[ORM\Table(name: 'media_items')]
class Media extends AbstractMedia
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    protected ?int $id = null;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true)]
    protected ?string $title = null;

    #[ORM\Column(type: Types::TEXT, length: 255, nullable: true)]
    protected ?string $description = null;

    #[ORM\Column(type: Types::STRING, length: 255)]
    protected ?string $provider = null;

    #[ORM\Column(type: Types::STRING, length: 255)]
    protected ?string $type = null;

    #[ORM\Column(type: Types::STRING, length: 255)]
    protected ?string $providerReference = null;

    #[ORM\Column(type: Types::JSON, length: 255)]
    protected array $providerMetaData = [];

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true)]
    protected ?string $image = null;

    #[ORM\Column(type: Types::JSON, length: 255)]
    protected array $imageMetaData = [];

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true)]
    protected ?string $focalPoint = null;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true)]
    protected ?string $copyright = null;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true)]
    protected ?string $authorName = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    protected DateTimeInterface $createdAt;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    protected DateTimeInterface $updatedAt;
}