Providers
=========

Bundled
-------

Out of the box this bundle features these providers:

- File, allows a user to upload a file
- Image, allows a user to upload an image
- YouTube, allows the user to paste a YouTube ID or URL
- Vimeo, allows the user to paste a Vimeo ID or URL
- SoundCloud, allows the user to paste a SoundCloud Url

Custom
------

You can add your own provider by implementing the *MediaMonks\SonataMediaBundle\Provider\ProviderInterface*. If your
provider supports oEmbed you can use the *MediaMonks\SonataMediaBundle\Provider\OembedProviderInterface*. However it is
recommended to extend *MediaMonks\SonataMediaBundle\Provider\AbstractProvider* or
*MediaMonks\SonataMediaBundle\Provider\AbstractOembedProvider*

When your provider is finished you can tag it with "sonata_media.provider" and it will be automatically added.

.. code-block:: yaml

    # config/services.yaml
    services:
        App\Provider\SomeProvider:
            tags:
                - { name: sonata_media.provider }

