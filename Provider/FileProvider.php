<?php

namespace MediaMonks\SonataMediaBundle\Provider;

use MediaMonks\SonataMediaBundle\Entity\Media;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Component\HttpFoundation\File\UploadedFile;
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
    public function getMediaTemplate()
    {
        return 'MediaMonksSonataMediaBundle:Provider:file_media.html.twig';
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
        switch ($extension) {
            case 'zip':
            case 'rar':
            case 'tar':
                return 'archive.png';
            case 'wav':
            case 'mp3':
            case 'flac':
            case 'aac':
            case 'aiff':
            case 'm4a':
            case 'ogg':
            case 'oga':
            case 'wma':
                return 'audio.png';
            case 'php':
            case 'html':
            case 'css':
            case 'js':
            case 'vb':
            case 'phar':
            case 'py':
            case 'jar':
            case 'json':
            case 'yml':
                return 'code.png';
            case 'xls':
            case 'xlt':
            case 'xlm':
            case 'xlsx':
            case 'xlsm':
            case 'xltx':
            case 'xltm':
                return 'excel.png';
            case 'jpg':
            case 'jpeg':
            case 'gif':
            case 'bmp':
            case 'tiff':
            case 'ai':
            case 'psd':
            case 'png':
                return 'image.png';
            case 'mp4':
            case 'avi':
            case 'mkv':
            case 'mpg':
            case 'mpeg':
                return 'movie.png';
            case 'pdf':
                return 'pdf.png';
            case 'ppt':
            case 'pot':
            case 'pps':
            case 'pptx':
            case 'pptm':
            case 'potx':
            case 'potm':
            case 'ppam':
            case 'ppsx':
            case 'ppsm':
            case 'sldx':
            case 'sldm':
                return 'powerpoint.png';
            case 'txt':
                return 'text.png';
            case 'doc':
            case 'dot':
            case 'wbk':
            case 'docx':
            case 'docm':
            case 'dotx':
            case 'dotm':
            case 'docb':
                return 'word.png';
            default:
                return 'default.png';
        }
    }

    /**
     * @return string
     */
    protected function getImageLocation()
    {
        return __DIR__.'/../Resources/image/file/';
    }
}
