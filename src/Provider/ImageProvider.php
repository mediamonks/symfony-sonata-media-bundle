<?php

namespace MediaMonks\SonataMediaBundle\Provider;

use MediaMonks\SonataMediaBundle\Model\AbstractMedia;
use Sonata\AdminBundle\Form\FormMapper;

class ImageProvider extends AbstractProvider implements ProviderInterface, DownloadableProviderInterface
{
    /**
     * @param FormMapper $formMapper
     */
    public function buildProviderCreateForm(FormMapper $formMapper)
    {
        $this->addRequiredImageField($formMapper, 'binaryContent', 'image');
    }

    /**
     * @param FormMapper $formMapper
     */
    public function buildProviderEditFormBefore(FormMapper $formMapper)
    {
        $this->addImageField($formMapper, 'binaryContent', 'image');
    }

    /**
     * @param FormMapper $formMapper
     */
    public function buildEditForm(FormMapper $formMapper)
    {
        parent::buildEditForm($formMapper);

        $formMapper->remove('imageContent');
    }

    /**
     * @param AbstractMedia $media
     * @param bool $providerReferenceUpdated
     */
    public function update(AbstractMedia $media, $providerReferenceUpdated)
    {
        if (!is_null($media->getBinaryContent())) {
            $media->setImage(null);
            $filename = $this->handleFileUpload($media, true);
            if (!empty($filename)) {
                $media->setProviderReference($filename);
            }
        }

        parent::update($media, $providerReferenceUpdated);
    }

    /**
     * @return string
     */
    public function getIcon(): string
    {
        return 'fa fa-photo';
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return 'image';
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return AbstractProvider::TYPE_IMAGE;
    }
}
