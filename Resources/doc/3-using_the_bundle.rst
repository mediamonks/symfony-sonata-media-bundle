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
