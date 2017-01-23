<?php

namespace MediaMonks\SonataMediaBundle\Provider;

use MediaMonks\SonataMediaBundle\Entity\Media;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Component\Validator\Constraints as Constraint;

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
     */
    public function update(Media $media)
    {
        if (!is_null($media->getBinaryContent())) {
            $media->setImage(null);
            $filename = $this->handleFileUpload($media, true);
            if (!empty($filename)) {
                $media->setProviderReference($filename);
            }
        }

        parent::update($media);
    }

    /**
     * @return string
     */
    public function getIcon()
    {
        return 'photo';
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
    public function getMediaTemplate()
    {
        return 'MediaMonksSonataMediaBundle:Provider:image_media.html.twig';
    }
}
