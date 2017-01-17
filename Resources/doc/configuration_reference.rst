MediaMonksSonataMediaBundle Configuration Reference
===================================================

All available configuration options are listed below with their default values.

.. code-block:: yaml

    mediamonks_sonata_media:
        filesystem: 'service_id_of_a_flysystem_filesystem'
        filesystem_cache: 'service_id_of_a_flysystem_filesystem'
        redirect_url: 'https://url.of.your.cdn/'
        redirect_cache_ttl: 7776000
        providers:
            - 'mediamonks.media.provider.image'
            - 'mediamonks.media.provider.youtube'
        glide:
            <any option supported by glide server>
