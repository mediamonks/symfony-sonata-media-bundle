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
        $this->addFileUploadField($formMapper, 'binaryContent', 'Image');
    }

    /**
     * @param Media $media
     */
    public function update(Media $media)
    {
        if (!is_null($media->getBinaryContent())) {
            $filename = $this->handleFileUpload($media, true);
            $media->setProviderReference($filename);
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
