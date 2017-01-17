<?php

namespace MediaMonks\SonataMediaBundle\Provider;

use MediaMonks\SonataMediaBundle\Entity\Media;
use MediaMonks\SonataMediaBundle\Model\MediaInterface;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Constraint;

class ImageProvider extends AbstractProvider
{
    /**
     * @param FormMapper $formMapper
     */
    public function buildEditForm(FormMapper $formMapper)
    {
        $formMapper
            ->with('General')
            ->add('providerName', HiddenType::class)
            ->add(
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
            ->add('tags')
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
    public function buildCreateForm(FormMapper $formMapper)
    {
        $formMapper
            ->add('providerName', HiddenType::class)
            ->add(
                'binaryContent',
                FileType::class,
                [
                    'multiple'    => false,
                    'data_class'  => null,
                    'constraints' => [
                        new Constraint\NotBlank(),
                        new Constraint\NotNull(),
                        new Constraint\File(),
                    ],
                    'label'       => 'Image',
                ]
            );
    }

    /**
     * @param MediaInterface $media
     */
    public function update(MediaInterface $media)
    {
        if (!is_null($media->getBinaryContent())) {
            $filename = $this->handleFileUpload($media);
            $media->setProviderReference($filename);
        }
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

        $stream = fopen($file->getRealPath(), 'r+');
        $this->getFilesystem()->writeStream($filename, $stream);
        @fclose($stream); // this sometime messes up

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
     * @param MediaInterface $media
     * @param array $options
     * @return array
     */
    public function toArray(MediaInterface $media, array $options = [])
    {
        return parent::toArray($media, $options) + [
                'type' => $this->getTypeName(),
            ];
    }

    /**
     * @return string
     */
    public function getIcon()
    {
        return 'photo';
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'Image';
    }

    public function getTypeName()
    {
        return 'image';
    }

    /**
     * @return string
     */
    public function getMediaTemplate()
    {
        return 'MediaMonksSonataMediaBundle:Provider:image_media.html.twig';
    }

    /**
     * @return string
     */
    public function getAdminMediaTemplate()
    {
        return 'MediaMonksSonataMediaBundle:Provider:image_media_admin.html.twig';
    }
}