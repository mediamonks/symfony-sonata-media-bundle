Step 3: Using the bundle
========================

Admin Usage
-----------

The most common use case will probably be to have other entities use media from this bundle.
For this you can use the regular sonata form types to load media entities but you can also use a fancier type:

.. code-block:: php

    # some Sonata admin class

    use MediaMonks\SonataMediaBundle\Form\Type\MediaAutocompleteType;
    use Sonata\AdminBundle\Form\FormMapper;

    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('media', MediaAutocompleteType::class)
            ->add('gallery', MediaAutocompleteType::class, [
                'multiple' => true
            ])
            ->add('image', MediaAutocompleteType::class, [
                'callback' => function ($admin, $property, $value) {
                    $datagrid = $admin->getDatagrid();
                    $queryBuilder = $datagrid->getQuery();
                    $queryBuilder
                        ->andWhere($queryBuilder->getRootAlias() . '.type = :type')
                        ->setParameter('type', 'image')
                    ;
                    $datagrid->setValue($property, null, $value);
                },
                'required' => false
            ])
        ;
    }

This form type will render a small preview image next to the title of the media object.

The example code shows 3 common use cases:
    - 'media' shows how you can choose a single media object (many to one)
    - 'gallery' shows you you can choose multiple media objects (many to many)
    - 'image' shows how you can choose a single media object of a specific type (many to one)

Displaying a media object with Twig
-----------------------------------

There are a few Twig filters available to display your media object:

The ``media_image`` filter will render an image and should be supported at all times:

.. code-block:: html+twig

    {{ media_object|media_image(800, 600) }}



The ``media_embed`` filter will render the full embed of the media, so for YouTube this will render a video player but for
SoundCloud this will render an audio player:

.. code-block:: html+twig

    {{ media_object|media_embed(800, 600) }}

Please note that not necessarily all media objects will support embed.


The ``media_download`` will render a download option for this type of media if supported:

.. code-block:: html+twig

    {{ media_object|media_image(800, 600) }}

To verify if your media supports any of the above features you can and should always test this before using one of
these filter methods with ``media_supports``:

.. code-block:: html+twig

    {% if media_object|media_supports('download') %}
        {{ media_object|media_image(800, 600) }}
    {% endif %}


You can also use the ``media`` filter to render the embed if possible but fall back to the


Generaring a custom url
-----------------------

With the url generator you can generate links to media with customized parameters:

.. code-block:: php

    # This example assumes you are inside a basic Symfony Framework controller, it's advised to inject these services instead

    use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

    // inside your controller action
    $media = $this->getDoctrine()->getManager()->find('MediaMonksSonataMediaBundle:Media', 1);
    $urlGenerator = $this->get('mediamonks.sonata_media.generator.url_generator');

    // generate path to a 400x300 image of this media
    $url = $this->get('mediamonks.sonata_media.generator.url_generator')->generate(
        $media,
        ['w' => 400, 'h' => 300],
    );

    // generate url to a 400x300 image of this media
    $url = $this->get('mediamonks.sonata_media.generator.url_generator')->generate(
        $media,
        ['w' => 400, 'h' => 300],
        null,
        UrlGeneratorInterface::ABSOLUTE_URL
    );

    // generate path to a 400x300 image of this media using a custom route name
    $url = $this->get('mediamonks.sonata_media.generator.url_generator')->generate(
        $media,
        ['w' => 400, 'h' => 300],
        'custom_route_name'
    );
