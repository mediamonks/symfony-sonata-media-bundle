<?php

namespace MediaMonks\SonataMediaBundle\Provider;

use League\Flysystem\Filesystem;
use MediaMonks\SonataMediaBundle\Entity\Media;
use MediaMonks\SonataMediaBundle\Model\MediaInterface;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\CoreBundle\Validator\ErrorElement;

interface ProviderInterface
{
    public function setFilesystem(Filesystem $filesystem);

    public function setImageConstraintOptions(array $options);

    public function buildCreateForm(FormMapper $formMapper);

    public function buildEditForm(FormMapper $formMapper);

    public function buildProviderCreateForm(FormMapper $formMapper);

    public function buildProviderEditForm(FormMapper $formMapper);

    public function update(Media $media, $providerReferenceUpdated);

    public function toArray(MediaInterface $media, array $options);

    public function getTitle();

    public function getName();

    public function getType();

    public function getIcon();

    public function getEmbedTemplate();

    public function getTranslationDomain();

    public function supports($renderType);

    public function supportsEmbed();

    public function supportsImage();

    public function supportsDownload();

    public function validate(ErrorElement $errorElement, Media $media);
}
