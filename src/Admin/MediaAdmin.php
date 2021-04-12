<?php

namespace MediaMonks\SonataMediaBundle\Admin;

use MediaMonks\SonataMediaBundle\Model\AbstractMedia;
use MediaMonks\SonataMediaBundle\Model\MediaInterface;
use MediaMonks\SonataMediaBundle\Provider\ProviderPool;
use MediaMonks\SonataMediaBundle\Provider\ProviderInterface;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\Form\Validator\ErrorElement;

class MediaAdmin extends AbstractAdmin
{
    /**
     * @var ProviderPool
     */
    private $providerPool;

    /**
     * @var string
     */
    private $originalProviderReference;

    /**
     * @var string
     */
    protected $baseRouteName = 'admin_mediamonks_sonatamedia_media';

    /**
     * @var string
     */
    protected $baseRoutePattern = 'mediamonks/sonatamedia/media';

    /**
     * @param string $code
     * @param string $class
     * @param string $baseControllerName
     * @param ProviderPool $providerPool
     */
    public function __construct($code, $class, $baseControllerName, ProviderPool $providerPool)
    {
        parent::__construct($code, $class, $baseControllerName);

        $this->providerPool = $providerPool;
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('title')
            ->add(
                'type'
            )
            ->add(
                'updatedAt'
            )
            ->add(
                '_action',
                'actions',
                [
                    'actions' => [
                        'edit'   => [],
                        'delete' => [],
                    ],
                ]
            );
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        /**
         * @var AbstractMedia $media
         */
        $media = $this->getSubject();
        if (!$media) {
            $media = $this->getNewInstance();
        }

        $this->originalProviderReference = $media->getProviderReference();

        $provider = $this->getProvider($media);
        $provider->setMedia($media);

        if ($media->getId()) {
            $provider->buildEditForm($formMapper);
        } else {
            $provider->buildCreateForm($formMapper);
        }
    }

    /**
     * @param AbstractMedia $media
     */
    public function prePersist($media)
    {
        $this->getProvider($media)->update($media, true);
    }

    /**
     * @param AbstractMedia $media
     */
    public function preUpdate($media)
    {
        $this->getProvider($media)->update($media, $this->isProviderReferenceUpdated($media));
    }

    /**
     * @param AbstractMedia $media
     * @return bool
     */
    protected function isProviderReferenceUpdated(AbstractMedia $media)
    {
        return $this->originalProviderReference !== $media->getProviderReference();
    }

    /**
     * @param AbstractMedia $media
     * @return ProviderInterface
     */
    protected function getProvider(AbstractMedia $media)
    {
        if (empty($media->getProvider())) {
            throw new \InvalidArgumentException('No provider was set');
        }

        $provider = $this->providerPool->getProvider($media->getProvider());
        $media->setType($provider->getType());

        return $this->providerPool->getProvider($media->getProvider());
    }

    /**
     * @return AbstractMedia
     */
    public function getNewInstance()
    {
        $media = parent::getNewInstance();
        $providerName = null;
        if ($this->hasRequest()) {
            if ($this->getRequest()->isMethod('POST')) {
                $providerName = $this->getRequest()->get($this->getUniqid())['provider'];
            } elseif ($this->getRequest()->query->has('provider')) {
                $providerName = $this->getRequest()->query->get('provider');
            }
        }

        if (!empty($providerName)) {
            $media->setProvider($providerName);
        }

        return $media;
    }

    /**
     * @param mixed $object
     * @return string
     */
    public function toString($object)
    {
        return $object instanceof MediaInterface ? $object->getTitle() : 'Media';
    }

    /**
     * @param RouteCollection $collection
     */
    public function configureRoutes(RouteCollection $collection)
    {
        $collection->add('image', $this->getRouterIdParameter().'/image/{width}/{height}');
        $collection->add('download', $this->getRouterIdParameter().'/download');
    }

    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('title')
            ->add('type')
            ->add('provider')
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function validate(ErrorElement $errorElement, $object)
    {
        $this->getProvider($object)->validate($errorElement, $object);
    }
}
