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

    public function getTitle();

    public function getType();

    public function getIcon();

    public function getMediaTemplate();

    public function getTranslationDomain();

    public function supports($type);

    public function supportsDownload();

    public function supportsEmbed();

    public function supportsImage();
}
