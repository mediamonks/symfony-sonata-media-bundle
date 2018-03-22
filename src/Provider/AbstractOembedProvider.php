<?php

namespace MediaMonks\SonataMediaBundle\Provider;

use MediaMonks\SonataMediaBundle\Model\AbstractMedia;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\CoreBundle\Validator\ErrorElement;
use Symfony\Component\Form\Extension\Core\Type\TextType;

abstract class AbstractOembedProvider extends AbstractProvider implements OembedProviderInterface, EmbeddableProviderInterface
{
    /**
     * @var array
     */
    protected $oembedDataCache;

    /**
     * @param AbstractMedia $media
     * @param bool $providerReferenceUpdated
     * @throws \Exception
     */
    public function update(AbstractMedia $media, $providerReferenceUpdated)
    {
        if ($providerReferenceUpdated) {
            $media->setProviderReference($this->parseProviderReference($media->getProviderReference()));
            $this->updateMediaObject($media);
        }

        parent::update($media, $providerReferenceUpdated);
    }

    /**
     * @param AbstractMedia $media
     */
    protected function updateMediaObject(AbstractMedia $media)
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
    public function buildProviderCreateForm(FormMapper $formMapper)
    {
        $formMapper->add(
            'providerReference',
            TextType::class,
            ['label' => $this->getReferenceLabel()]
        );
    }

    /**
     * @param FormMapper $formMapper
     */
    public function buildProviderEditFormBefore(FormMapper $formMapper)
    {
        $formMapper->add(
            'providerReference',
            TextType::class,
            ['label' => $this->getReferenceLabel()]
        );
    }

    /**
     * @param ErrorElement $errorElement
     * @param AbstractMedia $media
     */
    public function validate(ErrorElement $errorElement, AbstractMedia $media)
    {
        try {
            $this->getOembedDataCache($this->parseProviderReference($media->getProviderReference()));
        }
        catch (\Exception $e) {
            $errorElement->with('providerReference')->addViolation($e->getMessage());
        }
    }

    /**
     * @param \MediaMonks\SonataMediaBundle\Model\AbstractMedia $media
     */
    public function refreshImage(AbstractMedia $media)
    {
        $filename = sprintf('%s_%d.%s', sha1($media->getProviderReference()), time(), 'jpg');
        $thumbnailUrl = $this->getImageUrl($media->getProviderReference());

        $this->getFilesystem()->write($filename, $this->getHttpClient()->getData($thumbnailUrl));

        $media->setImage($filename);
    }

    /**
     * @param string $id
     * @return string
     */
    public function getImageUrl($id): string
    {
        return $this->getOembedDataCache($id)['thumbnail_url'];
    }

    /**
     * @param $id
     * @return mixed
     * @throws \Exception
     */
    protected function getOembedDataCache($id)
    {
        if (empty($this->oembedDataCache[$id])) {

            $this->disableErrorHandler();
            $data = json_decode($this->getHttpClient()->getData($this->getOembedUrl($id)), true);
            $this->restoreErrorHandler();

            if (empty($data['title'])) {
                throw new \Exception($this->getTranslator()->trans('error.provider_reference', [
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
