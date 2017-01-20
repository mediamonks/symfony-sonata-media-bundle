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
    }

    /**
     * @param MediaInterface $media
     * @param array $options
     * @return array
     */
    public function toArray(MediaInterface $media, array $options = [])
    {
        return [
            'type'        => $this->getTypeName(),
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
            ->add('providerName', 'hidden');

        $this->buildProviderCreateForm($formMapper);
    }

    /**
     * @param FormMapper $formMapper
     */
    public function buildEditForm(FormMapper $formMapper)
    {
        $formMapper
            ->with('General')
            ->add('providerName', HiddenType::class);

        $this->buildProviderEditForm($formMapper);

        $formMapper->add(
            'binaryContent',
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
     * @return string
     * @throws \Exception
     */
    protected function handleFileUpload(Media $media)
    {
        /**
         * @var UploadedFile $file
         */
        $file = $media->getBinaryContent();

        $filename = sprintf(
            '%s_%d.%s',
            sha1($file->getClientOriginalName()),
            time(),
            $file->getClientOriginalExtension()
        );

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

        $media->setProviderMetadata(
            array_merge(
                $media->getProviderMetaData(),
                [
                    'originalName'      => $file->getClientOriginalName(),
                    'originalExtension' => $file->getClientOriginalExtension(),
                    'mimeType'          => $file->getClientMimeType(),
                    'size'              => $file->getSize(),
                ]
            )
        );
        $media->setImage($filename);

        if (empty($media->getTitle())) {
            $media->setTitle(str_replace('_', ' ', pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)));
        }

        return $filename;
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

    // @todo handle image replacement

    // @todo handle binary upload replacement
}
