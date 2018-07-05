<?php

namespace MediaMonks\SonataMediaBundle\Form\Type;

use MediaMonks\SonataMediaBundle\Admin\MediaAdmin;
use Sonata\AdminBundle\Form\Type\ModelAutocompleteType as BaseModelAutocompleteType;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Twig\Environment;

class MediaAutocompleteType extends BaseModelAutocompleteType
{
    /**
     * @var MediaAdmin
     */
    private $mediaAdmin;

    /**
     * @var Environment
     */
    private $twig;

    /**
     * @param MediaAdmin $mediaAdmin
     * @param Environment $twig
     */
    public function __construct(MediaAdmin $mediaAdmin, Environment $twig)
    {
        $this->mediaAdmin = $mediaAdmin;
        $this->twig = $twig;
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
                    return $this->twig->render(
                        '@MediaMonksSonataMedia/CRUD/autocomplete.html.twig',
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
