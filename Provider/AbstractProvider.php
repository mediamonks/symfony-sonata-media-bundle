<?php

namespace MediaMonks\SonataMediaBundle\Provider;

use League\Flysystem\Filesystem;
use MediaMonks\SonataMediaBundle\Model\AbstractMedia;
use MediaMonks\SonataMediaBundle\Form\Type\MediaFocalPointType;
use MediaMonks\SonataMediaBundle\Model\MediaInterface;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\CoreBundle\Validator\ErrorElement;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Validator\Constraints as Constraint;

abstract class AbstractProvider implements ProviderInterface
{
    const SUPPORT_EMBED = 'embed';
    const SUPPORT_IMAGE = 'image';
    const SUPPORT_DOWNLOAD = 'download';

    const TYPE_AUDIO = 'audio';
    const TYPE_IMAGE = 'image';
    const TYPE_FILE = 'file';
    const TYPE_VIDEO = 'video';

    /**
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var array
     */
    private $imageConstraintOptions = [];

    /**
     * @var AbstractMedia
     */
    private $media;

    /**
     * @param Filesystem $filesystem
     */
    public function setFilesystem(Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;
    }

    /**
     * @param TranslatorInterface $translator
     */
    public function setTranslator(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * @return \Symfony\Component\Translation\TranslatorInterface
     */
    public function getTranslator()
    {
        return $this->translator;
    }

    /**
     * @param array $options
     */
    public function setImageConstraintOptions(array $options)
    {
        $this->imageConstraintOptions = $options;
    }

    /**
     * @return Filesystem
     */
    public function getFilesystem()
    {
        return $this->filesystem;
    }

    /**
     * @param AbstractMedia $media
     * @return AbstractProvider
     */
    public function setMedia($media)
    {
        $this->media = $media;

        return $this;
    }

    /**
     * @param AbstractMedia $media
     * @param $providerReferenceUpdated
     */
    public function update(AbstractMedia $media, $providerReferenceUpdated)
    {
        $this->updateImage($media);
    }

    /**
     * @return string
     */
    public function getTranslationDomain()
    {
        return 'MediaMonksSonataMediaBundle';
    }

    /**
     * @param MediaInterface $media
     * @param array $options
     * @return array
     */
    public function toArray(MediaInterface $media, array $options = [])
    {
        return [
            'type'        => $this->getName(),
            'title'       => $media->getTitle(),
            'description' => $media->getDescription(),
            'authorName'  => $media->getAuthorName(),
            'copyright'   => $media->getCopyright(),
            'reference'   => $media->getProviderReference(),
        ];
    }

    /**
     * @param FormMapper $formMapper
     */
    public function buildCreateForm(FormMapper $formMapper)
    {
        $formMapper
            ->add('provider', 'hidden');

        $this->buildProviderCreateForm($formMapper);
    }

    /**
     * @param FormMapper $formMapper
     */
    public function buildEditForm(FormMapper $formMapper)
    {
        $formMapper
            ->tab('General')
            ->add('provider', HiddenType::class);

        $this->buildProviderEditFormBefore($formMapper);

        $formMapper->add(
            'imageContent',
            'file',
            [
                'required'    => false,
                'constraints' => [
                    new Constraint\File(),
                ],
                'label'       => 'form.replacement_image',
            ]
        )
            ->add('title', TextType::class, ['label' => 'form.title'])
            ->add(
                'description',
                TextType::class,
                ['label' => 'form.description', 'required' => false]
            )
            ->add(
                'authorName',
                TextType::class,
                ['label' => 'form.authorName', 'required' => false]
            )
            ->add(
                'copyright',
                TextType::class,
                ['label' => 'form.copyright', 'required' => false]
            )
            ->end()
            ->end()
            ->tab('Image')
            ->add('focalPoint', MediaFocalPointType::class, [
                'media' => $this->media
            ])
            ->end()
            ->end()
        ;

        $this->buildProviderEditFormAfter($formMapper);
    }

    /**
     * @param FormMapper $formMapper
     */
    public function buildProviderEditFormBefore(FormMapper $formMapper)
    {
    }

    /**
     * @param FormMapper $formMapper
     */
    public function buildProviderEditFormAfter(FormMapper $formMapper)
    {
    }

    /**
     * @param AbstractMedia $media
     * @param bool $useAsImage
     * @return string|void
     */
    protected function handleFileUpload(AbstractMedia $media, $useAsImage = false)
    {
        /**
         * @var UploadedFile $file
         */
        $file = $media->getBinaryContent();

        if (empty($file)) {
            return;
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
     * @return array
     */
    protected function getFileMetaData(UploadedFile $file)
    {
        $fileData = [
            'originalName'      => $file->getClientOriginalName(),
            'originalExtension' => $file->getClientOriginalExtension(),
            'mimeType'          => $file->getClientMimeType(),
            'size'              => $file->getSize(),
        ];

        if (strpos($file->getClientMimeType(), 'image') !== false) {
            $this->disableErrorHandler();
            $imageSize = getimagesize($file->getRealPath());
            if (is_array($imageSize)) {
                list($width, $height) = $imageSize;
                if (is_int($width) && is_int($height)) {
                    $fileData['height'] = $height;
                    $fileData['width']  = $width;
                }
            }
            $this->restoreErrorHandler();
        }

        return $fileData;
    }

    /**
     * @param AbstractMedia $media
     */
    public function updateImage(AbstractMedia $media)
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
     * @param \Sonata\AdminBundle\Form\FormMapper $formMapper
     * @param $name
     * @param $label
     * @param $required
     * @param array $constraints
     */
    protected function doAddFileField(
        FormMapper $formMapper,
        $name,
        $label,
        $required,
        $constraints = []
    ) {
        if ($required) {
            $constraints = array_merge(
                [
                    new Constraint\NotBlank(),
                    new Constraint\NotNull(),
                ],
                $constraints
            );
        }

        $formMapper
            ->add(
                $name,
                FileType::class,
                [
                    'multiple'    => false,
                    'data_class'  => null,
                    'constraints' => $constraints,
                    'label'       => $label,
                    'required'    => $required,
                ]
            );
    }

    /**
     * @param FormMapper $formMapper
     * @param string $name
     * @param string $label
     * @param array $options
     */
    public function addImageField(
        FormMapper $formMapper,
        $name,
        $label,
        $options = []
    ) {
        $this->doAddFileField(
            $formMapper,
            $name,
            $label,
            false,
            [
                new Constraint\Image(
                    array_merge($this->imageConstraintOptions, $options)
                ),
            ]
        );
    }

    /**
     * @param FormMapper $formMapper
     * @param $name
     * @param $label
     * @param array $options
     */
    public function addRequiredImageField(
        FormMapper $formMapper,
        $name,
        $label,
        $options = []
    ) {
        $this->doAddFileField(
            $formMapper,
            $name,
            $label,
            true,
            [
                new Constraint\Image(
                    array_merge($this->imageConstraintOptions, $options)
                ),
            ]
        );
    }

    /**
     * @param UploadedFile $file
     * @return string
     */
    protected function getFilenameByFile(UploadedFile $file)
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
     * @param $filename
     * @throws \Exception
     */
    protected function writeToFilesystem(UploadedFile $file, $filename)
    {
        $this->disableErrorHandler();
        $stream = fopen($file->getRealPath(), 'r+');
        $written = $this->getFilesystem()->writeStream($filename, $stream);
        fclose($stream); // this sometime messes up
        $this->restoreErrorHandler();

        if (!$written) {
            throw new \Exception('Could not write to file system');
        }
    }

    /**
     * @param $renderType
     * @return boolean
     */
    public function supports($renderType)
    {
        if ($renderType === self::SUPPORT_EMBED) {
            return $this->supportsEmbed();
        }
        if ($renderType === self::SUPPORT_IMAGE) {
            return $this->supportsImage();
        }
        if ($renderType === self::SUPPORT_DOWNLOAD) {
            return $this->supportsDownload();
        }

        return false;
    }

    /**
     *
     */
    protected function disableErrorHandler()
    {
        set_error_handler(
            function () {
            }
        );
    }

    /**
     *
     */
    protected function restoreErrorHandler()
    {
        restore_error_handler();
    }

    /**
     * @param ErrorElement $errorElement
     * @param AbstractMedia $media
     */
    public function validate(ErrorElement $errorElement, AbstractMedia $media)
    {
    }

    /**
     * @return string
     */
    public function getEmbedTemplate()
    {
        return sprintf(
            'MediaMonksSonataMediaBundle:Provider:%s_embed.html.twig',
            $this->getName()
        );
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->translator->trans($this->getName());
    }
}
