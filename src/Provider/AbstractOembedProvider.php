<?php

namespace MediaMonks\SonataMediaBundle\Provider;

use Exception;
use League\Flysystem\FilesystemException;
use MediaMonks\SonataMediaBundle\Model\AbstractMedia;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\Form\Validator\ErrorElement;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Throwable;

abstract class AbstractOembedProvider extends AbstractProvider implements OembedProviderInterface, EmbeddableProviderInterface
{
    protected array $oembedDataCache = [];

    /**
     * @param AbstractMedia $media
     * @param string|null $providerReferenceUpdated
     *
     * @return void
     * @throws FilesystemException
     * @throws Throwable
     * @throws \League\Glide\Filesystem\FilesystemException
     */
    public function update(AbstractMedia $media, ?string $providerReferenceUpdated = null): void
    {
        if ($providerReferenceUpdated) {
            $media->setProviderReference($this->parseProviderReference($media->getProviderReference()));
            $this->updateMediaObject($media);
        }

        parent::update($media, $providerReferenceUpdated);
    }

    /**
     * @param AbstractMedia $media
     *
     * @throws FilesystemException|Throwable
     */
    protected function updateMediaObject(AbstractMedia $media): void
    {
        $data = $this->getOembedDataCache($media->getProviderReference());

        $media->setProviderMetaData($data);

        if (empty($media->getTitle()) && isset($data['title'])) {
            $media->setTitle($data['title']);
        }
        if (empty($media->getDescription()) && isset($data['description'])) {
            $media->setDescription($data['description']);
        }
        if (empty($media->getAuthorName()) && isset($data['author_name'])) {
            $media->setAuthorName($data['author_name']);
        }

        if (empty($media->getImage())) {
            $this->refreshImage($media);
        }
    }

    /**
     * @param FormMapper $formMapper
     */
    public function buildProviderCreateForm(FormMapper $formMapper): void
    {
        $formMapper->add('providerReference', TextType::class, [
            'label' => $this->getReferenceLabel()
        ]);
    }

    /**
     * @param FormMapper $formMapper
     */
    public function buildProviderEditFormBefore(FormMapper $formMapper): void
    {
        $formMapper->add('providerReference', TextType::class, [
            'label' => $this->getReferenceLabel()
        ]);
    }

    /**
     * @param ErrorElement $errorElement
     * @param AbstractMedia $media
     */
    public function validate(ErrorElement $errorElement, AbstractMedia $media): void
    {
        try {
            $this->getOembedDataCache($this->parseProviderReference($media->getProviderReference()));
        } catch (Exception $e) {
            $errorElement->with('providerReference')->addViolation($e->getMessage());
        }
    }

    /**
     * @param AbstractMedia $media
     *
     * @throws FilesystemException|Throwable
     */
    public function refreshImage(AbstractMedia $media): void
    {
        $filename = sprintf(
            '%s_%d.%s',
            sha1($media->getProviderReference()),
            time(),
            'jpg'
        );
        $thumbnailUrl = $this->getImageUrl($media->getProviderReference());

        $this->getFilesystem()->write($filename, $this->getHttpClient()->get($thumbnailUrl));

        $media->setImage($filename);
    }

    /**
     * @param string $id
     *
     * @return string
     * @throws Exception
     */
    public function getImageUrl(string $id): string
    {
        return $this->getOembedDataCache($id)['thumbnail_url'];
    }

    /**
     * @param string $id
     *
     * @return mixed
     * @throws Exception
     */
    protected function getOembedDataCache(string $id)
    {
        if (empty($this->oembedDataCache[$id])) {
            $this->disableErrorHandler();
            $data = json_decode($this->getHttpClient()->get($this->getOembedUrl($id)), true);
            $this->restoreErrorHandler();

            if (empty($data['title'])) {
                throw new Exception($this->getTranslator()->trans('error.provider_reference', [
                    '%provider%' => $this->getName(),
                    '%reference%' => $id
                ]));
            }

            $this->oembedDataCache[$id] = $data;
        }

        return $this->oembedDataCache[$id];
    }

    /**
     * @return string
     */
    public function getReferenceLabel(): string
    {
        return sprintf('form.%s.reference', $this->getName());
    }
}
