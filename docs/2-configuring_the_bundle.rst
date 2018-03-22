Step 2: Configuring the bundle
==============================


Providers
---------

A provider supports some kind of media. By default the bundle loads all available providers which are provided by
this bundle. You can add your own provider by tagging it as "sonata_media.provider".


File Systems
------------

This bundle requires you to have 2 configured filesystems with Flysystem. The "private" file system stores all
original uploads while the "public" file system stores all thumbnails. You can of course use the same file system
for both but this can be risky since you might allow uploads that can harm the machine the file is stored on (eg:
allow a php file to be uploaded and to be executed by the end user!). For the configuration of Flysystem we recommend
using the `Flysystem Bundle`_.

.. code-block:: yaml

    # app/config/config.yml
    mediamonks_sonata_media:
        filesystem_private: <service id of a flysystem file system>
        filesystem_public: <service id of a flysystem file system>

Redirect
--------

This bundle assumes you are using some kind of storage that is behind a CDN or a reverse proxy that supports cache
control headers. The benefit of this is that after an image is generated it does not hit our server again until it
expires. By default the ttl is set to 90 days.

.. code-block:: yaml

    # app/config/config.yml
    mediamonks_sonata_media:
        redirect_url: "<https://path.to.public.file.system/>"
        redirect_cache_ttl: 7776000


Image Parameters
----------------

While it is possible to set custom options while generating an image per media object you might want to always have some
default settings for the Glide Api:

.. code-block:: yaml

    # app/config/config.yml
    mediamonks_sonata_media:
        default_image_parameters:
            fit: "crop"


Image Constraints
-----------------

You can set image constraints to avoid bad quality media, below are the default settings:

.. code-block:: yaml

    # app/config/config.yml
    mediamonks_sonata_media:
        image_constraints:
            minWidth: 100
            minHeight: 100
            maxWidth: 5000
            maxHeight: 5000


File Constraints
----------------

Since this can be a really dangerous provider it is very restrictive by default. Please carefully consider to allow
file uploads of any file type. Below are the default settings:

.. code-block:: yaml

    # app/config/config.yml
    mediamonks_sonata_media:
        file_constraints:
            maxSize: '5M'
            extensions: ['pdf', 'csv', 'txt', 'docx']

Model Class
-----------

The bundle assumes that your entity is using FQCN ``App\Entity\Media``, if your entity is using a different
FQCN you can use the option ``model_class`` to set it.

.. code-block:: yaml

    # app/config/config.yml
    mediamonks_sonata_media:
        model_class: Acme\AppBundle\Entity\Media

Model Class
-----------

Override or extend the admin class with your own to modify behavior.

.. code-block:: yaml

    # app/config/config.yml
    mediamonks_sonata_media:
        admin_class: App\Admin\MediaAdmin


Controller Class
----------------

Override or extend the controller class with your own to modify behavior.

.. code-block:: yaml

    # app/config/config.yml
    mediamonks_sonata_media:
        admin_class: App\Controller\Admin\MediaCRUDController


.. _Flysystem Bundle: https://github.com/1up-lab/OneupFlysystemBundle
