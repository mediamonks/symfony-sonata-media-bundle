<?php

namespace MediaMonks\SonataMediaBundle\Provider;

use League\Glide\Filesystem\FilesystemException;
use MediaMonks\SonataMediaBundle\Model\AbstractMedia;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Constraint;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class FileProvider extends AbstractProvider implements ProviderInterface, DownloadableProviderInterface
{
    private array $fileConstraintOptions = [];

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
    public function buildProviderCreateForm(FormMapper $formMapper): void
    {
        $this->addRequiredFileField($formMapper, 'binaryContent', 'file');
    }

    /**
     * @param FormMapper $formMapper
     */
    public function buildProviderEditFormBefore(FormMapper $formMapper): void
    {
        $this->addFileField($formMapper, 'binaryContent', 'file');
    }

    /**
     * @param AbstractMedia $media
     * @param bool $providerReferenceUpdated
     *
     * @return void
     * @throws FilesystemException
     */
    public function update(AbstractMedia $media, bool $providerReferenceUpdated = false): void
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
    public function getIcon(): string
    {
        return 'fa fa-file';
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return 'file';
    }

    /**
     * @return string
     */
    public function getType(): string
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
     * @param string|null $label
     * @param array $options
     */
    public function addFileField(FormMapper $formMapper, string $name, ?string $label = null, array $options = [])
    {
        $this->doAddFileField($formMapper, $name, $label, false, $this->getFileFieldConstraints($options));
    }

    /**
     * @param FormMapper $formMapper
     * @param string $name
     * @param string|null $label
     * @param array $options
     */
    public function addRequiredFileField(FormMapper $formMapper, string $name, ?string $label = null, array $options = [])
    {
        $this->doAddFileField($formMapper, $name, $label, true, $this->getFileFieldConstraints($options));
    }

    /**
     * @param array $options
     *
     * @return array
     */
    private function getFileFieldConstraints(array $options): array
    {
        return [
            new Constraint\File($this->getFileConstraintOptions($options)),
            new Constraint\Callback([$this, 'validateExtension']),
        ];
    }

    /**
     * @param array $options
     *
     * @return array
     */
    protected function getFileConstraintOptions(array $options = []): array
    {
        $merged = array_merge($this->fileConstraintOptions, $options);
        unset($merged['extensions']);

        return $merged;
    }

    /**
     * @param $object
     * @param ExecutionContextInterface $context
     */
    public function validateExtension($object, ExecutionContextInterface $context): void
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
     *
     * @return string
     */
    protected function getImageByExtension($extension): string
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
    protected function getArchiveExtensions(): array
    {
        return ['zip', 'rar', 'tar', 'gz'];
    }

    /**
     * @return string[]
     */
    protected function getAudioExtensions(): array
    {
        return ['wav', 'mp3', 'flac', 'aac', 'aiff', 'm4a', 'ogg', 'oga', 'wma'];
    }

    /**
     * @return string[]
     */
    protected function getCodeExtensions(): array
    {
        return ['php', 'html', 'css', 'js', 'vb', 'phar', 'py', 'jar', 'json', 'yml'];
    }

    /**
     * @return string[]
     */
    protected function getSpreadsheetExtensions(): array
    {
        return ['xls', 'xlt', 'xlm', 'xlsx', 'xlsm', 'xltx', 'xltm'];
    }

    /**
     * @return string[]
     */
    protected function getImageExtensions(): array
    {
        return ['png', 'jpg', 'jpeg', 'gif', 'bmp', 'tiff', 'ai', 'psd'];
    }

    /**
     * @return string[]
     */
    protected function getMovieExtensions(): array
    {
        return ['mp4', 'avi', 'mkv', 'mpg', 'mpeg'];
    }

    /**
     * @return string[]
     */
    protected function getPdfExtensions(): array
    {
        return ['pdf'];
    }

    /**
     * @return string[]
     */
    protected function getPresentationExtensions(): array
    {
        return ['ppt', 'pot', 'pos', 'pps', 'pptx', 'pptm', 'potx', 'potm', 'ppam', 'ppsx', 'ppsm', 'sldx', 'sldm'];
    }

    /**
     * @return string[]
     */
    protected function getTextExtensions(): array
    {
        return ['txt', 'csv'];
    }

    /**
     * @return string[]
     */
    protected function getWordExtensions(): array
    {
        return ['doc', 'dot', 'wbk', 'docx', 'docm', 'dotx', 'dotm', 'docb'];
    }

    /**
     * @param $imageFilename
     *
     * @return string
     */
    protected function getImageLocation($imageFilename): string
    {
        $file = $this->getFileLocator()->locate('@MediaMonksSonataMediaBundle/Resources/image/file/' . $imageFilename);
        if (is_array($file)) {
            $file = current($file);
        }

        return $file;
    }
}
