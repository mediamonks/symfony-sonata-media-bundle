<?php

namespace MediaMonks\SonataMediaBundle\Provider;

use MediaMonks\SonataMediaBundle\Entity\Media;
use MediaMonks\SonataMediaBundle\Model\MediaInterface;
use Sonata\AdminBundle\Form\FormMapper;

interface ProviderInterface
{
    public function buildProviderEditForm(FormMapper $formMapper);

    public function buildProviderCreateForm(FormMapper $formMapper);

    public function preUpdate(Media $media);

    public function prePersist(Media $media);

    public function update(Media $media);

    public function toArray(MediaInterface $media, array $options);

    public function getName();

    public function getTypeName();

    public function getIcon();

    public function getMediaTemplate();
}
