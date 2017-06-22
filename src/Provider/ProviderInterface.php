<?php

namespace MediaMonks\SonataMediaBundle\Provider;

use League\Flysystem\Filesystem;
use MediaMonks\SonataMediaBundle\Client\HttpClientInterface;
use MediaMonks\SonataMediaBundle\Model\AbstractMedia;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\CoreBundle\Validator\ErrorElement;
use Symfony\Component\HttpKernel\Config\FileLocator;
use Symfony\Component\Translation\TranslatorInterface;

interface ProviderInterface
{
    public function setFilesystem(Filesystem $filesystem);

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

    public function supports($renderType);

    public function supportsEmbed();

    public function supportsImage();

    public function supportsDownload();

    public function validate(ErrorElement $errorElement, AbstractMedia $media);
}
