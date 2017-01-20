<?php

namespace MediaMonks\SonataMediaBundle\Provider;

use MediaMonks\SonataMediaBundle\Entity\Media;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Component\Validator\Constraints as Constraint;

class FileProvider extends AbstractProvider
{
    /**
     * @param FormMapper $formMapper
     */
    public function buildProviderCreateForm(FormMapper $formMapper)
    {
        $this->addFileUploadField($formMapper, 'binaryContent', 'File');
    }

    /**
     * @param Media $media
     */
    public function update(Media $media)
    {
        if (!is_null($media->getBinaryContent())) {
            $filename = $this->handleFileUpload($media);
            $media->setProviderReference($filename);
        }
        if (!is_null($media->getImageContent())) {
            $this->handleImageUpload($media);
        }
    }

    /**
     * @return string
     */
    public function getIcon()
    {
        return 'file';
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'File';
    }

    public function getTypeName()
    {
        return 'file';
    }

    /**
     * @return string
     */
    public function getMediaTemplate()
    {
        return 'MediaMonksSonataMediaBundle:Provider:file_media.html.twig';
    }
}
