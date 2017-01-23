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
        $this->addRequiredFileUploadField($formMapper, 'binaryContent', 'File');
    }

    /**
     * @param FormMapper $formMapper
     */
    public function buildProviderEditForm(FormMapper $formMapper)
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
        return 'file';
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return 'File';
    }

    public function getType()
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
