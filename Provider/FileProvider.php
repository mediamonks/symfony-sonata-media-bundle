<?php

namespace MediaMonks\SonataMediaBundle\Provider;

use MediaMonks\SonataMediaBundle\Entity\Media;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Component\HttpFoundation\File\UploadedFile;

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
            if (empty($media->getImage())) {
                $this->setFileImage($media);
            }
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
    public function getEmbedTemplate()
    {
        return 'MediaMonksSonataMediaBundle:Provider:file_embed.html.twig';
    }

    /**
     * @param Media $media
     */
    protected function setFileImage(Media $media)
    {
        /**
         * @var UploadedFile $file
         */
        $file = $media->getBinaryContent();
        if (empty($file)) {
            return;
        }

        $imageFilename = $this->getImageByExtension($file->getClientOriginalExtension());
        $media->setImageContent(
            new UploadedFile(
                $this->getImageLocation().$imageFilename,
                $imageFilename
            )
        );
    }

    /**
     * @param $extension
     * @return string
     */
    protected function getImageByExtension($extension)
    {
        if (in_array($extension, ['zip', 'rar', 'tar', 'gz'])) {
            return 'archive.png';
        }
        if (in_array($extension, ['wav', 'mp3', 'flac', 'aac', 'aiff', 'm4a', 'ogg', 'oga', 'wma'])) {
            return 'audio.png';
        }
        if (in_array($extension, ['php', 'html', 'css', 'js', 'vb', 'phar', 'py', 'jar', 'json', 'yml'])) {
            return 'code.png';
        }
        if (in_array($extension, ['xls', 'xlt', 'xlm', 'xlsx', 'xlsm', 'xltx', 'xltm'])) {
            return 'excel.png';
        }
        if (in_array($extension, ['png', 'jpg', 'jpeg', 'gif', 'bmp', 'tiff', 'ai', 'psd'])) {
            return 'image.png';
        }
        if (in_array($extension, ['mp4', 'avi', 'mkv', 'mpg', 'mpeg'])) {
            return 'movie.png';
        }
        if (in_array($extension, ['pdf'])) {
            return 'pdf.png';
        }
        if (in_array(
            $extension,
            ['ppt', 'pot', 'pos', 'pps', 'pptx', 'pptm', 'potx', 'potm', 'ppam', 'ppsx', 'ppsm', 'sldx', 'sldm']
        )) {
            return 'powerpoint.png';
        }
        if (in_array($extension, ['txt'])) {
            return 'txt.png';
        }
        if (in_array($extension, ['doc', 'dot', 'wbk', 'docx', 'docm', 'dotx', 'dotm', 'docb'])) {
            return 'word.png';
        }

        return 'default.png';
    }

    /**
     * @return string
     */
    protected function getImageLocation()
    {
        return __DIR__.'/../Resources/image/file/';
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
