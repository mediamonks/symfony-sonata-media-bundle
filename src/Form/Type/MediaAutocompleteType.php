<?php

namespace MediaMonks\SonataMediaBundle\Form\Type;

use MediaMonks\SonataMediaBundle\Model\MediaInterface;
use Sonata\AdminBundle\Form\Type\ModelAutocompleteType as BaseModelAutocompleteType;
use Sonata\AdminBundle\Model\ModelManagerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class MediaAutocompleteType extends AbstractType
{
    const DEFAULT_AUTOCOMPLETE_ROUTE = 'mediamonks_media_autocomplete';

    private ModelManagerInterface $modelManager;
    private Environment $twig;
    private string $mediaEntityClass;

    /**
     * @param ModelManagerInterface $modelManager
     * @param Environment $twig
     * @param string $mediaEntityClass
     */
    public function __construct(
        ModelManagerInterface $modelManager,
        Environment $twig,
        string $mediaEntityClass
    )
    {
        $this->modelManager = $modelManager;
        $this->twig = $twig;
        $this->mediaEntityClass = $mediaEntityClass;
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
                'model_manager' => $this->modelManager,
                'class' => $this->mediaEntityClass
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

    public function getParent()
    {
        return BaseModelAutocompleteType::class;
    }

}
