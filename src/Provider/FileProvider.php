<?php

namespace MediaMonks\SonataMediaBundle\Provider;

use MediaMonks\SonataMediaBundle\Model\AbstractMedia;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Constraint;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class FileProvider extends AbstractProvider implements ProviderInterface, DownloadableProviderInterface
{
    /**
     * @var array
     */
    private $fileConstraintOptions = [];

    /**
     * @param array $fileConstraintOptions
     */
    public function __construct(array $fileConstraintOptions = [])
    {
        $this->fileConstraintOptions = $fileConstraintOptions;
    }

    /**
     * @param FormMapper $formMapper
     */
    public function buildProviderCreateForm(FormMapper $formMapper)
    {
        $this->addRequiredFileField($formMapper, 'binaryContent', 'file');
    }

    /**
     * @param FormMapper $formMapper
     */
    public function buildProviderEditFormBefore(FormMapper $formMapper)
    {
        $this->addFileField($formMapper, 'binaryContent', 'file');
    }

    /**
     * @param AbstractMedia $media
     * @param bool $providerReferenceUpdated
     */
    public function update(AbstractMedia $media, $providerReferenceUpdated)
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

        parent::update($media, $providerReferenceUpdated);
    }

    /**
     * @return string
     */
    public function getIcon()
    {
        return 'fa fa-file';
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'file';
    }

    /**
     * @return string
     */
    public function getType()
    {
        return AbstractProvider::TYPE_FILE;
    }

    /**
     * @param AbstractMedia $media
     */
    protected function setFileImage(AbstractMedia $media)
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
                $this->getImageLocation($imageFilename),
                $imageFilename
            )
        );
    }

    /**
     * @param FormMapper $formMapper
     * @param string $name
     * @param string $label
     * @param array $options
     */
    public function addFileField(FormMapper $formMapper, $name, $label, $options = [])
    {
        $this->doAddFileField($formMapper, $name, $label, false, $this->getFileFieldConstraints($options));
    }

    /**
     * @param FormMapper $formMapper
     * @param string $name
     * @param string $label
     * @param array $options
     */
    public function addRequiredFileField(FormMapper $formMapper, $name, $label, $options = [])
    {
        $this->doAddFileField($formMapper, $name, $label, true, $this->getFileFieldConstraints($options));
    }

    /**
     * @param array $options
     * @return array
     */
    private function getFileFieldConstraints(array $options)
    {
        return [
            new Constraint\File($this->getFileConstraintOptions($options)),
            new Constraint\Callback([$this, 'validateExtension']),
        ];
    }

    /**
     * @param array $options
     * @return array
     */
    protected function getFileConstraintOptions(array $options = [])
    {
        $merged = array_merge($this->fileConstraintOptions, $options);
        unset($merged['extensions']);

        return $merged;
    }

    /**
     * @param $object
     * @param ExecutionContextInterface $context
     */
    public function validateExtension($object, ExecutionContextInterface $context)
    {
        if ($object instanceof UploadedFile && isset($this->fileConstraintOptions['extensions'])) {
            if (!in_array($object->getClientOriginalExtension(), $this->fileConstraintOptions['extensions'])) {
                $context->addViolation(
                    sprintf(
                        'It\'s not allowed to upload a file with extension "%s"',
                        $object->getClientOriginalExtension()
                    )
                );
            }
        }
    }

    /**
     * @param $extension
     * @return string
     */
    protected function getImageByExtension($extension)
    {
        if (in_array($extension, $this->getArchiveExtensions())) {
            return 'archive.png';
        }
        if (in_array($extension, $this->getAudioExtensions())) {
            return 'audio.png';
        }
        if (in_array($extension, $this->getCodeExtensions())) {
            return 'code.png';
        }
        if (in_array($extension, $this->getSpreadsheetExtensions())) {
            return 'excel.png';
        }
        if (in_array($extension, $this->getImageExtensions())) {
            return 'image.png';
        }
        if (in_array($extension, $this->getMovieExtensions())) {
            return 'movie.png';
        }
        if (in_array($extension, $this->getPdfExtensions())) {
            return 'pdf.png';
        }
        if (in_array($extension, $this->getPresentationExtensions())) {
            return 'powerpoint.png';
        }
        if (in_array($extension, $this->getTextExtensions())) {
            return 'text.png';
        }
        if (in_array($extension, $this->getWordExtensions())) {
            return 'word.png';
        }

        return 'default.png';
    }

    /**
     * @return string[]
     */
    private function getArchiveExtensions()
    {
        return ['zip', 'rar', 'tar', 'gz'];
    }

    /**
     * @return string[]
     */
    private function getAudioExtensions()
    {
        return ['wav', 'mp3', 'flac', 'aac', 'aiff', 'm4a', 'ogg', 'oga', 'wma'];
    }

    /**
     * @return string[]
     */
    private function getCodeExtensions()
    {
        return ['php', 'html', 'css', 'js', 'vb', 'phar', 'py', 'jar', 'json', 'yml'];
    }

    /**
     * @return string[]
     */
    private function getSpreadsheetExtensions()
    {
        return ['xls', 'xlt', 'xlm', 'xlsx', 'xlsm', 'xltx', 'xltm'];
    }

    /**
     * @return string[]
     */
    private function getImageExtensions()
    {
        return ['png', 'jpg', 'jpeg', 'gif', 'bmp', 'tiff', 'ai', 'psd'];
    }

    /**
     * @return string[]
     */
    private function getMovieExtensions()
    {
        return ['mp4', 'avi', 'mkv', 'mpg', 'mpeg'];
    }

    /**
     * @return string[]
     */
    private function getPdfExtensions()
    {
        return ['pdf'];
    }

    /**
     * @return string[]
     */
    private function getPresentationExtensions()
    {
        return ['ppt', 'pot', 'pos', 'pps', 'pptx', 'pptm', 'potx', 'potm', 'ppam', 'ppsx', 'ppsm', 'sldx', 'sldm'];
    }

    /**
     * @return string[]
     */
    private function getTextExtensions()
    {
        return ['txt', 'csv'];
    }

    /**
     * @return string[]
     */
    private function getWordExtensions()
    {
        return ['doc', 'dot', 'wbk', 'docx', 'docm', 'dotx', 'dotm', 'docb'];
    }

    /**
     * @param $imageFilename
     * @return string
     */
    protected function getImageLocation($imageFilename)
    {
        $file = $this->getFileLocator()->locate('@MediaMonksSonataMediaBundle/Resources/image/file/'.$imageFilename);
        if (is_array($file)) {
            $file = current($file);
        }

        return $file;
    }
}
