<?php

namespace MediaMonks\SonataMediaBundle\Entity;

use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use MediaMonks\SonataMediaBundle\Model\AbstractMedia;

class MediaLegacy extends AbstractMedia
{
    /**
     * @var ?int
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected ?int $id = null;

    /**
     * @var ?string
     * @ORM\Column(type="string", nullable=true)
     */
    protected ?string $title = null;

    /**
     * @var ?string
     * @ORM\Column(type="text", nullable=true)
     */
    protected ?string $description = null;

    /**
     * @var ?string
     * @ORM\Column(type="string")
     */
    protected ?string $provider = null;

    /**
     * @var ?string
     * @ORM\Column(type="string")
     */
    protected ?string $type = null;

    /**
     * @var ?string
     * @ORM\Column(type="string")
     */
    protected ?string $providerReference = null;

    /**
     * @var array
     * @ORM\Column(type="json")
     */
    protected array $providerMetaData = [];

    /**
     * @var ?string
     * @ORM\Column(type="string", nullable=true)
     */
    protected ?string $image = null;

    /**
     * @var array
     * @ORM\Column(type="json")
     */
    protected array $imageMetaData = [];

    /**
     * @var ?string
     * @ORM\Column(type="string", nullable=true)
     */
    protected ?string $focalPoint = null;

    /**
     * @var ?string
     * @ORM\Column(type="string", nullable=true)
     */
    protected ?string $copyright = null;

    /**
     * @var ?string
     * @ORM\Column(type="string", nullable=true)
     */
    protected ?string $authorName = null;

    /**
     * @var DateTimeInterface
     * @ORM\Column(type="datetime")
     */
    protected DateTimeInterface $createdAt;

    /**
     * @var DateTimeInterface
     * @ORM\Column(type="datetime")
     */
    protected DateTimeInterface $updatedAt;
}
