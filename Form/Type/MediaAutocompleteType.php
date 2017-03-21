<?php

namespace MediaMonks\SonataMediaBundle\Form\Type;

use MediaMonks\SonataMediaBundle\Admin\MediaAdmin;
use Sonata\AdminBundle\Form\Type\ModelAutocompleteType as BaseModelAutocompleteType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Templating\EngineInterface;

class MediaAutocompleteType extends BaseModelAutocompleteType
{
    /**
     * @var MediaAdmin
     */
    private $mediaAdmin;

    /**
     * @var EngineInterface
     */
    private $templateEngine;

    /**
     * @param MediaAdmin $mediaAdmin
     * @param EngineInterface $templateEngine
     */
    public function __construct(MediaAdmin $mediaAdmin, EngineInterface $templateEngine)
    {
        $this->mediaAdmin = $mediaAdmin;
        $this->templateEngine = $templateEngine;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults(
            [
                'property' => 'title',
                'to_string_callback' => function ($media, $property) {
                    return $this->templateEngine->render(
                        'MediaMonksSonataMediaBundle:MediaAdmin:autocomplete.html.twig',
                        [
                            'media' => $media,
                        ]
                    );
                },
                'route' => ['name' => 'mediamonks_media_autocomplete', 'parameters' => []],
                'model_manager' => $this->mediaAdmin->getModelManager()
            ]
        );
    }
}
