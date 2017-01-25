<?php

namespace MediaMonks\SonataMediaBundle\Provider;

use MediaMonks\SonataMediaBundle\Entity\Media;
use Sonata\AdminBundle\Form\FormMapper;

class ImageProvider extends AbstractProvider
{
    /**
     * @param FormMapper $formMapper
     */
    public function buildProviderCreateForm(FormMapper $formMapper)
    {
        $this->addRequiredFileUploadField($formMapper, 'binaryContent', 'Image');
    }

    /**
     * @param FormMapper $formMapper
     */
    public function buildProviderEditForm(FormMapper $formMapper)
    {
        $this->addFileUploadField($formMapper, 'binaryContent', 'Image');
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
     * @param Media $media
     * @param bool $providerReferenceUpdated
     */
    public function update(Media $media, $providerReferenceUpdated)
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
    public function getIcon()
    {
        return 'fa fa-photo';
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return 'Image';
    }

    public function getType()
    {
        return 'image';
    }

    /**
     * @return string
     */
    public function getEmbedTemplate()
    {
        return 'MediaMonksSonataMediaBundle:Provider:image_embed.html.twig';
    }

    /**
     * @return bool
     */
    public function supportsDownload()
    {
        return true;
    }

    /**
     * @return bool
     */
    public function supportsEmbed()
    {
        return false;
    }

    /**
     * @return bool
     */
    public function supportsImage()
    {
        return true;
    }
}
