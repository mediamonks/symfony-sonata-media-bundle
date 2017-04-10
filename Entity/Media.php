<?php

namespace MediaMonks\SonataMediaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use MediaMonks\SonataMediaBundle\Model\AbstractMedia;

/**
 * @ORM\Entity(repositoryClass="MediaMonks\SonataMediaBundle\Repository\MediaRepository")
 * @ORM\Table(name="media")
 */
class Media extends AbstractMedia
{
    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $title;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $description;

    /**
     * @ORM\Column(type="string")
     */
    protected $provider;

    /**
     * @ORM\Column(type="string")
     */
    protected $type;

    /**
     * @ORM\Column(type="string")
     */
    protected $providerReference;

    /**
     * @ORM\Column(type="json_array")
     */
    protected $providerMetaData = [];

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $image;

    /**
     * @ORM\Column(type="json_array")
     */
    protected $imageMetaData = [];

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $focalPoint;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $copyright;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $authorName;

    /**
     * @var \DateTime
     * @ORM\Column(type="datetime")
     */
    protected $createdAt;

    /**
     * @var \DateTime
     * @ORM\Column(type="datetime")
     */
    protected $updatedAt;
}
