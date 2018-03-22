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
                'type' => 'image',
                'required' => false
            ])
            ->add('youtube', MediaAutocompleteType::class, [
                'provider' => 'youtube',
                'required' => false
            ])
        ;
    }

This form type will render a small preview image next to the title of the media object.

The example code shows some common use cases:
    - 'media' shows how you can choose a single media object (many to one)
    - 'gallery' shows you you can choose multiple media objects (many to many)
    - 'image' shows how you can choose a single media object of a specific type (many to one)
    - 'soundcloud' shows how you can choose a single media object of a specific provider (many to one)

Displaying a media object with Twig
-----------------------------------

There are a few Twig filters available to display your media object:

The ``media_image`` filter will render an image and should be supported at all times:

.. code-block:: html+twig

    {{ media_object|media_image(800, 600) }}

Every media should always support to render an image.

You can optionally pass some extra filters which will be applied to the rendered image:

.. code-block:: html+twig

    {{ media_object|media_image(800, 600, {blur: 5}) }}


The ``media_embed`` filter will render the full embed of the media,
so for YouTube this will render a video player but for SoundCloud this will render an audio player.

.. code-block:: html+twig

    {{ media_object|media_embed(800, 600) }}

Please note that not necessarily all media objects will support this.


The ``media_download`` will render a download option for this type of media if supported:

.. code-block:: html+twig

    {{ media_object|media_download(800, 600) }}

Please note that not necessarily all media objects will support this.

To verify if your media supports any of the above features you can and should always test this before using one of
these filter methods with ``media_supports``:

.. code-block:: html+twig

    {% if media_object is media_downloadable %}
        {{ media_object|media_image(800, 600) }}
    {% endif %}

You can also use the ``media`` filter to render the embed if possible, it will fall back to rendering an
image if embedding is not available:

.. code-block:: html+twig

    {{ media_object|media(800, 600) }}

Or if you want to be absolutely sure the media is only rendered as embed you can test if this is supported

.. code-block:: html_twig

    {% if media_object is media_embeddable %}
        {{ media_object|media_embed(800, 600) }}
    {% endif %}


Generaring a custom url
-----------------------

With the url generator you can generate links to media with customized parameters:

.. code-block:: php

    # This example assumes you are inside a basic Symfony Framework controller,
    # it's advised to inject these services instead

    use App\Entity\Media;
    use MediaMonks\SonataMediaBundle\Generator\ImageUrlGenerator;
    use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

    // inside your controller action
    $media = $this->getDoctrine()->getManager()->find(Media::class, 1);
    $urlGenerator = $this->get(ImageUrlGenerator::class);

    // generate path to a 400x300 image of this media
    $url = $urlGenerator->generateImageUrl($media, 400, 300);

    // generate path to a blurred 400x300 image of this media
    $url = $urlGenerator->generateImageUrl($media, 400, 300, ['blur' => 5]);

    // generate url to a 400x300 image of this media
    $url = $urlGenerator->generateImageUrl($media, 400, 300, [], null, UrlGeneratorInterface::ABSOLUTE_URL);

    // generate path to a 400x300 image of this media using a custom route name
    $url = $urlGenerator->generateImageUrl($media, 400, 300, [], 'custom_route_name');

For linking to a download you can use the download url generator instead:

.. code-block:: php

    # This example assumes you are inside a basic Symfony Framework controller,
    # it's advised to inject these services instead

    use App\Entity\Media;
    use MediaMonks\SonataMediaBundle\Generator\DownloadUrlGenerator;
    use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

    // inside your controller action
    $media = $this->getDoctrine()->getManager()->find(Media::class, 1);
    $urlGenerator = $this->get(DownloadUrlGenerator::class);

    // generate path to download this media
    $url = $urlGenerator->generateDownloadUrl($media);

    // generate an absolute url to download this media
    $url = $urlGenerator->generateDownloadUrl($media, null, UrlGeneratorInterface::ABSOLUTE_URL);

    // generate an absolute url to download this media by using a custom route name
    $url = $urlGenerator->generateDownloadUrl($media, 'custom_route_name');
