MediaMonksSonataMediaBundle Configuration Reference
===================================================

All available configuration options are listed below with their default values.

.. code-block:: yaml

    mediamonks_sonata_media:
        # service_id_of_a_flysystem_filesystem
        filesystem_private:
        # service_id_of_a_flysystem_filesystem
        filesystem_public:
        # redirect url to where files are stored
        redirect_url: 'https://url-to-where-your-files-are-stored/'
        # cache expire time which is set in the header of the redirect
        redirect_cache_ttl: 7776000
        # list of providers, by default all available providers are available
        providers:
            - 'MediaMonks\SonataMediaBundle\Provider\ImageProvider'
            - 'MediaMonks\SonataMediaBundle\Provider\FileProvider'
            - 'MediaMonks\SonataMediaBundle\Provider\YouTubeProvider'
            - 'MediaMonks\SonataMediaBundle\Provider\VimeoProvider'
            - 'MediaMonks\SonataMediaBundle\Provider\SoundCloudProvider'
        # an array with options supported by glide server
        glide: []
        # any parameter supported by glide image api
        default_image_parameters: []
        # any parameter supported by symfony image constraint
        image_constraints:
            - minWidth: 100
            - minHeight: 100
            - maxWidth: 3000
            - maxHeight: 3000
        # any parameter supported by symfony file constraint
        file_constraints:
            - maxSize: '5M'
            - extensions: ['pdf', 'csv', 'txt', 'docx']
        # path to an image file
        fallback_image: ~
        # path to local file system with read and write permission, defaults to sys_get_temp_dir() function
        tmp_path: ~
        # custom prefix for temporary files
        tmp_prefix: ~
        # route name used for image generation
        route_image: mediamonks_media_image
        # route name for downloads
        route_download: mediamonks_media_download
        # fqcn of the model
        model_class: App\Entity\Media
        # fqcn of the admin
        admin_class: MediaMonks\SonataMediaBundle\Admin\MediaAdmin
        # fqcn of the controller
        controller_class: MediaMonks\SonataMediaBundle\Controller\CRUDController
