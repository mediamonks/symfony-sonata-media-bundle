<?php

namespace MediaMonks\SonataMediaBundle\Provider;

use MediaMonks\SonataMediaBundle\Model\MediaInterface;
use Sonata\AdminBundle\Form\FormMapper;

interface ProviderInterface
{
    public function buildEditForm(FormMapper $formMapper);

    public function buildCreateForm(FormMapper $formMapper);

    public function preUpdate(MediaInterface $media);

    public function prePersist(MediaInterface $media);

    public function update(MediaInterface $media);

    public function toArray(MediaInterface $media, array $options);

    public function getName();

    public function getTypeName();

    public function getIcon();

    public function getMediaTemplate();

    public function getAdminMediaTemplate();
}