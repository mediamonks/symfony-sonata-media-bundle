<?php

namespace MediaMonks\MediaBundle\Provider;

use Intervention\Image\ImageManagerStatic;
use League\Flysystem\Filesystem;
use MediaMonks\MediaBundle\Model\MediaInterface;
use Monolog\Logger;
use Sonata\AdminBundle\Form\FormMapper;

abstract class AbstractProvider implements ProviderInterface
{
    /**
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * @var Logger
     */
    protected $logger;

    /**
     * @var array
     */
    protected $templates = [];

    /**
     * AbstractProvider constructor.
     * @param Filesystem $filesystem
     */
    public function __construct(Filesystem $filesystem, Logger $logger)
    {
        $this->filesystem = $filesystem;
        $this->logger     = $logger;
    }

    /**
     * @return Filesystem
     */
    public function getFilesystem()
    {
        return $this->filesystem;
    }

    /**
     * @param MediaInterface $media
     */
    public function prePersist(MediaInterface $media)
    {
        $this->update($media);
    }

    /**
     * @param MediaInterface $media
     */
    public function preUpdate(MediaInterface $media)
    {
        $this->update($media);
    }

    /**
     * @param MediaInterface $media
     */
    public function update(MediaInterface $media)
    {
    }

    /**
     * @param MediaInterface $media
     * @param array $options
     * @return array
     */
    public function toArray(MediaInterface $media, array $options = [])
    {
        return [
            'type'        => $this->getTypeName(),
            'title'       => $media->getTitle(),
            'description' => $media->getDescription(),
            'authorName'  => $media->getAuthorName(),
            'copyright'   => $media->getCopyright(),
            'tags'        => $media->getTags()
        ];
    }

    /**
     * @param $name
     * @return string
     */
    public function getTemplate($name)
    {
        return $this->templates[$name];
    }

    /**
     * @return array
     */
    protected function getPointOfInterestChoices()
    {
        return array_flip([
            'top-left' => 'Top Left',
            'top' => 'Top',
            'top-right' => 'Top Right',
            'left' => 'Left',
            'center' => 'Center',
            'right' => 'Right',
            'bottom-left' => 'Bottom Left',
            'bottom' => 'Bottom',
            'bottom-right' => 'Bottom Right'
        ]);
    }
}