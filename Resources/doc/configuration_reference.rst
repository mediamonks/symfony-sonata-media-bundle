MediaMonksSonataMediaBundle Configuration Reference
===================================================

All available configuration options are listed below with their default values.

.. code-block:: yaml

    mediamonks_sonata_media:
        filesystem_private: 'service_id_of_a_flysystem_filesystem'
        filesystem_public: 'service_id_of_a_flysystem_filesystem'
        redirect_url: 'https://url-to-where-your-files-are-stored/'
        redirect_cache_ttl: 7776000
        providers:
            - 'mediamonks.sonata_media.provider.image'
            - 'mediamonks.sonata_media.provider.file'
            - 'mediamonks.sonata_media.provider.youtube'
            - 'mediamonks.sonata_media.provider.soundcloud'
        glide:
            <any option supported by glide server>
        default_image_parameters:
            <any parameter supported by glide image api>
        image_constraints:
            <any parameter supported by symfony image constraint>
        file_constraints:
            <any parameter supported by symfony file constraint>
            extensions: <an array of supported extensions>
        fallback_image: <path to an image file>
        tmp_path: <path to local file system with read and write permission>
        tmp_prefix: <custom prefix for temporary files>
