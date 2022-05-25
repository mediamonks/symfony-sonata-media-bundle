<?php

namespace MediaMonks\SonataMediaBundle\Form\Type;

use MediaMonks\SonataMediaBundle\Admin\MediaAdmin;
use MediaMonks\SonataMediaBundle\Model\MediaInterface;
use Sonata\AdminBundle\Form\Type\ModelAutocompleteType as BaseModelAutocompleteType;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class MediaAutocompleteType extends BaseModelAutocompleteType
{
    const DEFAULT_AUTOCOMPLETE_ROUTE = 'mediamonks_media_autocomplete';

    private MediaAdmin $mediaAdmin;
    private Environment $twig;

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
    public function configureOptions(OptionsResolver $resolver): void
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
                'to_string_callback' => [$this, 'renderMediaToAutocomplete'],
                'route' => [$this, 'getAutocompleteRoute'],
                'model_manager' => $this->mediaAdmin->getModelManager()
            ]
        );
    }

    /**
     * @param MediaInterface $media
     * @param string|null $property
     *
     * @return string
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function renderMediaToAutocomplete(MediaInterface $media, ?string $property = null): string
    {
        return $this->twig->render(
            '@MediaMonksSonataMedia/CRUD/autocomplete.html.twig',
            [
                'media' => $media,
            ]
        );
    }

    /**
     * @param Options $options
     *
     * @return array
     */
    public function getAutocompleteRoute(Options $options): array
    {
        $parameters = [];
        if (isset($options['type'])) {
            $parameters['type'] = $options['type'];
        }
        if (isset($options['provider'])) {
            $parameters['provider'] = $options['provider'];
        }

        return ['name' => static::DEFAULT_AUTOCOMPLETE_ROUTE, 'parameters' => $parameters];
    }
}
