<?php

namespace MediaMonks\SonataMediaBundle\Provider;

use League\Flysystem\FilesystemOperator;
use League\Glide\Filesystem\FilesystemException;
use MediaMonks\SonataMediaBundle\Client\HttpClientInterface;
use MediaMonks\SonataMediaBundle\ErrorHandlerTrait;
use MediaMonks\SonataMediaBundle\Form\Type\MediaFocalPointType;
use MediaMonks\SonataMediaBundle\Model\AbstractMedia;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\Form\Validator\ErrorElement;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpKernel\Config\FileLocator;
use Symfony\Component\Validator\Constraints as Constraint;
use Symfony\Contracts\Translation\TranslatorInterface;
use Throwable;

abstract class AbstractProvider implements ProviderInterface
{
    use ErrorHandlerTrait;

    const SUPPORT_EMBED = 'embed';
    const SUPPORT_IMAGE = 'image';
    const SUPPORT_DOWNLOAD = 'download';

    const TYPE_AUDIO = 'audio';
    const TYPE_IMAGE = 'image';
    const TYPE_FILE = 'file';
    const TYPE_VIDEO = 'video';

    protected ?FilesystemOperator $filesystem = null;
    private ?TranslatorInterface $translator = null;
    private array $imageConstraintOptions = [];
    private ?HttpClientInterface $httpClient = null;
    private ?FileLocator $fileLocator = null;
    private ?AbstractMedia $media = null;

    /**
     * @param FilesystemOperator $filesystem
     */
    public function setFilesystem(FilesystemOperator $filesystem): void
    {
        $this->filesystem = $filesystem;
    }

    /**
     * @param TranslatorInterface $translator
     */
    public function setTranslator(TranslatorInterface $translator): void
    {
        $this->translator = $translator;
    }

    /**
     * @param array $options
     */
    public function setImageConstraintOptions(array $options): void
    {
        $this->imageConstraintOptions = $options;
    }

    /**
     * @param HttpClientInterface $httpClient
     */
    public function setHttpClient(HttpClientInterface $httpClient): void
    {
        $this->httpClient = $httpClient;
    }

    /**
     * @param FileLocator $fileLocator
     */
    public function setFileLocator(FileLocator $fileLocator): void
    {
        $this->fileLocator = $fileLocator;
    }

    /**
     * @return FilesystemOperator|null
     */
    public function getFilesystem(): ?FilesystemOperator
    {
        return $this->filesystem;
    }

    /**
     * @return TranslatorInterface|null
     */
    public function getTranslator(): ?TranslatorInterface
    {
        return $this->translator;
    }

    /**
     * @return HttpClientInterface|null
     */
    public function getHttpClient(): ?HttpClientInterface
    {
        return $this->httpClient;
    }

    /**
     * @return FileLocator|null
     */
    public function getFileLocator(): ?FileLocator
    {
        return $this->fileLocator;
    }

    /**
     * @param AbstractMedia $media
     *
     * @return AbstractProvider
     */
    public function setMedia(AbstractMedia $media): AbstractProvider
    {
        $this->media = $media;

        return $this;
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
        $this->updateImage($media);
    }

    /**
     * @param FormMapper $formMapper
     */
    public function buildCreateForm(FormMapper $formMapper): void
    {
        $formMapper
            ->add('provider', HiddenType::class);

        $this->buildProviderCreateForm($formMapper);
    }

    /**
     * @param FormMapper $formMapper
     */
    public function buildEditForm(FormMapper $formMapper): void
    {
        $formMapper
            ->tab('general')
            ->add('provider', HiddenType::class);

        $this->buildProviderEditFormBefore($formMapper);

        $formMapper
            ->add('imageContent', FileType::class, [
                'required' => false,
                'constraints' => [
                    new Constraint\File(),
                ],
                'label' => 'form.replacement_image',
            ])->add('title', TextType::class, [
                'label' => 'form.title'
            ])
            ->add('description', TextType::class, [
                'label' => 'form.description', 'required' => false
            ])
            ->add('authorName', TextType::class, [
                'label' => 'form.authorName', 'required' => false
            ])
            ->add('copyright', TextType::class, [
                'label' => 'form.copyright', 'required' => false
            ])
            ->end()
            ->end();

        $formMapper
            ->tab('image')
            ->add('focalPoint', MediaFocalPointType::class, [
                'media' => $this->media
            ])
            ->end()
            ->end();

        $this->buildProviderEditFormAfter($formMapper);
    }

    /**
     * @param FormMapper $formMapper
     *
     * @codeCoverageIgnore
     */
    public function buildProviderEditFormBefore(FormMapper $formMapper): void
    {
    }

    /**
     * @param FormMapper $formMapper
     *
     * @codeCoverageIgnore
     */
    public function buildProviderEditFormAfter(FormMapper $formMapper): void
    {
    }

    /**
     * @param AbstractMedia $media
     * @param bool $useAsImage
     *
     * @return string|null returns the generated filename on successful upload.
     * @throws FilesystemException
     */
    protected function handleFileUpload(AbstractMedia $media, bool $useAsImage = false): ?string
    {
        /**
         * @var UploadedFile $file
         */
        $file = $media->getBinaryContent();

        if (empty($file)) {
            return null;
        }

        $filename = $this->getFilenameByFile($file);
        $this->writeToFilesystem($file, $filename);

        $media->setProviderMetadata(
            array_merge(
                $media->getProviderMetaData(),
                $this->getFileMetaData($file)
            )
        );

        if (empty($media->getImage()) && $useAsImage) {
            $media->setImage($filename);
            $media->setImageMetaData($media->getProviderMetaData());
        }

        if (empty($media->getTitle())) {
            $media->setTitle(
                str_replace(
                    '_',
                    ' ',
                    pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)
                )
            );
        }

        return $filename;
    }

    /**
     * @param UploadedFile $file
     *
     * @return array
     */
    protected function getFileMetaData(UploadedFile $file): array
    {
        $fileData = [
            'originalName' => $file->getClientOriginalName(),
            'originalExtension' => $file->getClientOriginalExtension(),
            'mimeType' => $file->getClientMimeType(),
            'size' => $file->getSize(),
        ];

        $this->disableErrorHandler();
        $imageSize = getimagesize($file->getRealPath());
        if (is_array($imageSize)) {
            if (is_int($imageSize[0]) && is_int($imageSize[1])) {
                $fileData['width'] = $imageSize[0];
                $fileData['height'] = $imageSize[1];
            }
            if (isset($imageSize['bits'])) {
                $fileData['bits'] = $imageSize['bits'];
            }
            if (isset($imageSize['channels'])) {
                $fileData['channels'] = $imageSize['channels'];
            }
        }
        $this->restoreErrorHandler();

        return $fileData;
    }

    /**
     * @param AbstractMedia $media
     *
     * @return void
     * @throws FilesystemException
     */
    public function updateImage(AbstractMedia $media): void
    {
        /**
         * @var UploadedFile $file
         */
        $file = $media->getImageContent();
        if (empty($file)) {
            return;
        }

        $filename = $this->getFilenameByFile($file);
        $this->writeToFilesystem($file, $filename);

        $media->setImage($filename);
        $media->setImageMetaData($this->getFileMetaData($file));
    }

    /**
     * @param FormMapper $formMapper
     * @param string $name
     * @param string|null $label
     * @param bool $required
     * @param array $constraints
     *
     * @return void
     */
    protected function doAddFileField(
        FormMapper $formMapper,
        string $name,
        ?string $label,
        bool $required,
        array $constraints = []
    ): void
    {
        if ($required) {
            $constraints = array_merge([
                new Constraint\NotBlank(),
                new Constraint\NotNull(),
            ], $constraints);
        }

        $formMapper->add($name, FileType::class, [
            'multiple' => false,
            'data_class' => null,
            'constraints' => $constraints,
            'label' => $label,
            'required' => $required,
        ]);
    }

    /**
     * @param FormMapper $formMapper
     * @param string $name
     * @param string|null $label
     * @param array $options
     *
     * @return void
     */
    public function addImageField(
        FormMapper $formMapper,
        string $name,
        ?string $label,
        array $options = []
    ): void
    {
        $this->doAddFileField($formMapper, $name, $label, false, [
            new Constraint\Image(array_merge($this->imageConstraintOptions, $options)),
        ]);
    }

    /**
     * @param FormMapper $formMapper
     * @param string $name
     * @param string|null $label
     * @param array $options
     *
     * @return void
     */
    public function addRequiredImageField(
        FormMapper $formMapper,
        string $name,
        ?string $label,
        array $options = []
    ): void
    {
        $this->doAddFileField($formMapper, $name, $label, true, [
            new Constraint\Image(array_merge($this->imageConstraintOptions, $options)),
        ]);
    }

    /**
     * @param UploadedFile $file
     *
     * @return string
     */
    protected function getFilenameByFile(UploadedFile $file): string
    {
        return sprintf(
            '%s_%d.%s',
            sha1($file->getClientOriginalName()),
            time(),
            $file->getClientOriginalExtension()
        );
    }

    /**
     * @param UploadedFile $file
     * @param string $filename
     *
     * @return void
     * @throws FilesystemException
     */
    protected function writeToFilesystem(UploadedFile $file, string $filename): void
    {
        $this->disableErrorHandler();
        $stream = fopen($file->getRealPath(), 'r');
        try {
            $this->getFilesystem()->writeStream($filename, $stream);
        } catch (Throwable $e) {
            throw new FilesystemException('Could not write to file system', 0, $e);
        } finally {
            if (is_resource($stream) and !feof($stream)) {
                fclose($stream);
            }
            $this->restoreErrorHandler();
        }
    }

    /**
     * @param ErrorElement $errorElement
     * @param AbstractMedia $media
     */
    public function validate(ErrorElement $errorElement, AbstractMedia $media): void
    {
    }

    /**
     * @return string
     */
    public function getEmbedTemplate(): string
    {
        return sprintf(
            '@MediaMonksSonataMedia/Provider/%s_embed.html.twig',
            $this->getName()
        );
    }
}
