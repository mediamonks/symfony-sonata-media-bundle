<?php

namespace MediaMonks\SonataMediaBundle\Provider;

use MediaMonks\SonataMediaBundle\Entity\Media;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\CoreBundle\Validator\ErrorElement;
use Symfony\Component\Form\Extension\Core\Type\TextType;

abstract class AbstractOembedProvider extends AbstractProvider implements OembedProviderInterface
{
    /**
     * @var array
     */
    protected $oembedData;

    /**
     * @param Media $media
     * @param bool $providerReferenceUpdated
     * @throws \Exception
     */
    public function update(Media $media, $providerReferenceUpdated)
    {
        if ($providerReferenceUpdated) {
            $media->setProviderReference($this->parseProviderReference($media->getProviderReference()));

            $data = $this->getOembedData($media->getProviderReference());

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

        parent::update($media, $providerReferenceUpdated);
    }

    /**
     * @param FormMapper $formMapper
     */
    public function buildProviderCreateForm(FormMapper $formMapper)
    {
        $formMapper->add(
            'providerReference',
            TextType::class,
            ['label' => sprintf('%s %s', $this->getTitle(), $this->getReferenceName())]
        );
    }

    /**
     * @param FormMapper $formMapper
     */
    public function buildProviderEditForm(FormMapper $formMapper)
    {
        $formMapper->add(
            'providerReference',
            TextType::class,
            ['label' => sprintf('%s %s', $this->getTitle(), $this->getReferenceName())]
        );
    }

    /**
     * @param ErrorElement $errorElement
     * @param Media $media
     */
    public function validate(ErrorElement $errorElement, Media $media)
    {
        try {
            $this->getOembedData($this->parseProviderReference($media->getProviderReference()));
        }
        catch (\Exception $e) {
            $errorElement->with('providerReference')->addViolation($e->getMessage());
        }
    }

    /**
     * @param \MediaMonks\SonataMediaBundle\Entity\Media $media
     */
    public function refreshImage(Media $media)
    {
        $filename = sprintf('%s_%d.%s', sha1($media->getProviderReference()), time(), 'jpg');
        $thumbnailUrl = $this->getImageUrl($media->getProviderReference());

        $this->getFilesystem()->write(
            $filename,
            file_get_contents($thumbnailUrl)
        );
        $media->setImage($filename);
    }

    /**
     * @param string $id
     * @return string
     */
    public function getImageUrl($id)
    {
        return $this->getOembedData($id)['thumbnail_url'];
    }

    /**
     * @param $id
     * @return mixed
     * @throws \Exception
     */
    protected function getOembedData($id)
    {
        if (empty($this->oembedData)) {

            $this->disableErrorHandler();
            $data = json_decode(file_get_contents($this->getOembedUrl($id)), true);
            $this->restoreErrorHandler();

            if (empty($data['title'])) {
                throw new \Exception(sprintf('%s %s "%s" seems to be incorrect', $this->getTitle(), $this->getReferenceName(), $id));
            }

            $this->oembedData = $data;
        }

        return $this->oembedData;
    }

    /**
     * @return string
     */
    public function getReferenceName()
    {
        return 'Reference';
    }

    /**
     * @return bool
     */
    public function supportsDownload()
    {
        return false;
    }

    /**
     * @return bool
     */
    public function supportsEmbed()
    {
        return true;
    }

    /**
     * @return bool
     */
    public function supportsImage()
    {
        return true;
    }
}
