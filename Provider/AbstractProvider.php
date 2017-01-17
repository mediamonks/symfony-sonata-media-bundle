<?php

namespace MediaMonks\SonataMediaBundle\Provider;

use League\Flysystem\Filesystem;
use MediaMonks\SonataMediaBundle\Model\MediaInterface;

abstract class AbstractProvider implements ProviderInterface
{
    /**
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * @var array
     */
    protected $templates = [];

    /**
     * @param Filesystem $filesystem
     */
    public function __construct(Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;
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
            'tags'        => $media->getTags(),
        ];
    }

    /**
     * @return array
     */
    protected function getPointOfInterestChoices()
    {
        return array_flip(
            [
                'top-left'     => 'Top Left',
                'top'          => 'Top',
                'top-right'    => 'Top Right',
                'left'         => 'Left',
                'center'       => 'Center',
                'right'        => 'Right',
                'bottom-left'  => 'Bottom Left',
                'bottom'       => 'Bottom',
                'bottom-right' => 'Bottom Right',
            ]
        );
    }
}