<?php

namespace MediaMonks\SonataMediaBundle\Provider;

use League\Flysystem\FilesystemOperator;
use MediaMonks\SonataMediaBundle\Client\HttpClientInterface;
use MediaMonks\SonataMediaBundle\Model\AbstractMedia;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\Form\Validator\ErrorElement;
use Symfony\Component\HttpKernel\Config\FileLocator;
use Symfony\Contracts\Translation\TranslatorInterface;

interface ProviderInterface
{
    public function setFilesystem(FilesystemOperator $filesystem): void;

    public function setImageConstraintOptions(array $options): void;

    public function setHttpClient(HttpClientInterface $httpClient): void;

    public function setTranslator(TranslatorInterface $translator): void;

    public function setFileLocator(FileLocator $fileLocator): void;

    public function buildCreateForm(FormMapper $formMapper): void;

    public function buildEditForm(FormMapper $formMapper): void;

    public function buildProviderCreateForm(FormMapper $formMapper): void;

    public function buildProviderEditFormBefore(FormMapper $formMapper): void;

    public function buildProviderEditFormAfter(FormMapper $formMapper): void;

    public function update(AbstractMedia $media, ?string $providerReferenceUpdated = null): void;

    public function getName(): string;

    public function getType(): string;

    public function getIcon(): string;

    public function validate(ErrorElement $errorElement, AbstractMedia $media): void;
}
