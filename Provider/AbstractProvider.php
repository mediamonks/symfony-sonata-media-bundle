<?php

namespace MediaMonks\SonataMediaBundle\Provider;

use League\Flysystem\Filesystem;
use MediaMonks\SonataMediaBundle\Entity\Media;
use MediaMonks\SonataMediaBundle\Model\MediaInterface;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Constraint;

abstract class AbstractProvider implements ProviderInterface
{
    /**
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * @var array
     */
    protected $templates = [];

    /**
     * @param Filesystem $filesystem
     */
    public function __construct(Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;
    }

    /**
     * @return Filesystem
     */
    public function getFilesystem()
    {
        return $this->filesystem;
    }

    /**
     * @param Media $media
     */
    public function prePersist(Media $media)
    {
        $this->update($media);
    }

    /**
     * @param Media $media
     */
    public function preUpdate(Media $media)
    {
        $this->update($media);
    }

    /**
     * @param Media $media
     */
    public function update(Media $media)
    {
        $this->replaceImage($media);
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
            'type'        => $this->getType(),
            'title'       => $media->getTitle(),
            'description' => $media->getDescription(),
            'authorName'  => $media->getAuthorName(),
            'copyright'   => $media->getCopyright(),
        ];
    }

    /**
     * @return array
     */
    protected function getPointOfInterestChoices()
    {
        return array_flip(
            [
                'top-left'     => 'Top Left',
                'top'          => 'Top',
                'top-right'    => 'Top Right',
                'left'         => 'Left',
                'center'       => 'Center',
                'right'        => 'Right',
                'bottom-left'  => 'Bottom Left',
                'bottom'       => 'Bottom',
                'bottom-right' => 'Bottom Right',
            ]
        );
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
            ->with('General')
            ->add('provider', HiddenType::class);

        $this->buildProviderEditForm($formMapper);

        $formMapper->add(
            'imageContent',
            'file',
            [
                'required'    => false,
                'constraints' => [
                    new Constraint\File(),
                ],
                'label'       => 'Replacement Image',
            ]
        )
            ->add('title')
            ->add('description')
            ->add('authorName')
            ->add('copyright')
            ->add(
                'pointOfInterest',
                ChoiceType::class,
                [
                    'required' => false,
                    'label'    => 'Point Of Interest',
                    'choices'  => $this->getPointOfInterestChoices(),
                ]
            )
            ->end();
    }

    /**
     * @param FormMapper $formMapper
     */
    public function buildProviderEditForm(FormMapper $formMapper)
    {
    }

    /**
     * @param Media $media
     * @param bool $useAsImage
     * @throws \Exception
     */
    protected function handleFileUpload(Media $media, $useAsImage = false)
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

        $media->setProviderMetadata(array_merge($media->getProviderMetaData(), $this->getFileMetaData($file)));

        if (empty($media->getImage()) && $useAsImage) {
            $media->setImage($filename);
            $media->setImageMetaData($media->getProviderMetaData());
        }

        if (empty($media->getTitle())) {
            $media->setTitle(str_replace('_', ' ', pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)));
        }
    }

    /**
     * @param UploadedFile $file
     * @return array
     */
    protected function getFileMetaData(UploadedFile $file)
    {
        return [
            'originalName'      => $file->getClientOriginalName(),
            'originalExtension' => $file->getClientOriginalExtension(),
            'mimeType'          => $file->getClientMimeType(),
            'size'              => $file->getSize(),
        ];
    }

    /**
     * @param Media $media
     */
    public function replaceImage(Media $media)
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
     * @param $name
     * @param $label
     */
    public function addFileUploadField(FormMapper $formMapper, $name, $label)
    {
        $formMapper
            ->add(
                $name,
                FileType::class,
                [
                    'multiple'    => false,
                    'data_class'  => null,
                    'constraints' => [
                        new Constraint\NotBlank(),
                        new Constraint\NotNull(),
                        new Constraint\File(),
                    ],
                    'label'       => $label,
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
        set_error_handler(
            function () {
            }
        );
        $stream = fopen($file->getRealPath(), 'r+');
        $written = $this->getFilesystem()->writeStream($filename, $stream);
        fclose($stream); // this sometime messes up
        restore_error_handler();

        if (!$written) {
            throw new \Exception('Could not write to file system');
        }
    }
}
