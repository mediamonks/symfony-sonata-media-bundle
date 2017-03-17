<?php

namespace MediaMonks\SonataMediaBundle\Form\Type;

use MediaMonks\SonataMediaBundle\Admin\MediaAdmin;
use Sonata\AdminBundle\Form\Type\ModelAutocompleteType as BaseModelAutocompleteType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Templating\EngineInterface;

class MediaAutocompleteType extends BaseModelAutocompleteType
{
    /**
     * @var EngineInterface
     */
    private $templateEngine;

    /**
     * @var MediaAdmin
     */
    private $mediaAdmin;

    /**
     * @param EngineInterface $templateEngine
     * @param MediaAdmin $mediaAdmin
     */
    public function __construct(EngineInterface $templateEngine, MediaAdmin $mediaAdmin)
    {
        $this->templateEngine = $templateEngine;
        $this->mediaAdmin = $mediaAdmin;
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
                'model_manager' => $this->mediaAdmin->getModelManager()
            ]
        );
    }
}
