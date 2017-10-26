<?php

namespace MediaMonks\SonataMediaBundle\Provider;

use League\Flysystem\FilesystemInterface;
use MediaMonks\SonataMediaBundle\Client\HttpClientInterface;
use MediaMonks\SonataMediaBundle\Model\AbstractMedia;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\CoreBundle\Validator\ErrorElement;
use Symfony\Component\HttpKernel\Config\FileLocator;
use Symfony\Component\Translation\TranslatorInterface;

interface ProviderInterface
{
    public function setFilesystem(FilesystemInterface $filesystem);

    public function setImageConstraintOptions(array $options);

    public function setHttpClient(HttpClientInterface $httpClient);

    public function setTranslator(TranslatorInterface $translator);

    public function setFileLocator(FileLocator $fileLocator);

    public function buildCreateForm(FormMapper $formMapper);

    public function buildEditForm(FormMapper $formMapper);

    public function buildProviderCreateForm(FormMapper $formMapper);

    public function buildProviderEditFormBefore(FormMapper $formMapper);

    public function buildProviderEditFormAfter(FormMapper $formMapper);

    public function update(AbstractMedia $media, $providerReferenceUpdated);

    public function getName();

    public function getType();

    public function getIcon();

    public function validate(ErrorElement $errorElement, AbstractMedia $media);
}
