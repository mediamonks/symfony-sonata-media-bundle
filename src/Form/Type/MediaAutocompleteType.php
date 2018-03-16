<?php

namespace MediaMonks\SonataMediaBundle\Form\Type;

use MediaMonks\SonataMediaBundle\Admin\MediaAdmin;
use Sonata\AdminBundle\Form\Type\ModelAutocompleteType as BaseModelAutocompleteType;
use Symfony\Component\OptionsResolver\Options;
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

        // Tell sonata we are passing a previously escaped string
        if ($resolver->hasDefault('safe_label')) {
            $resolver->setDefault('safe_label', true);
        }

        $resolver->setDefaults(
            [
                'type' => null,
                'provider' => null,
                'property' => 'title',
                'to_string_callback' => function ($media, $property) {
                    return $this->templateEngine->render(
                        'MediaMonksSonataMediaBundle:CRUD:autocomplete.html.twig',
                        [
                            'media' => $media,
                        ]
                    );
                },
                'route' => function(Options $options) {
                    $parameters = [];
                    if (isset($options['type'])) {
                        $parameters['type'] = $options['type'];
                    }
                    if (isset($options['provider'])) {
                        $parameters['provider'] = $options['provider'];
                    }
                    return ['name' => 'mediamonks_media_autocomplete', 'parameters' => $parameters];
                },
                'model_manager' => $this->mediaAdmin->getModelManager()
            ]
        );
    }
}
