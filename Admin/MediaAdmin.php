<?php

namespace MediaMonks\SonataMediaBundle\Admin;

use MediaMonks\SonataMediaBundle\Entity\Media;
use MediaMonks\SonataMediaBundle\Model\MediaInterface;
use MediaMonks\SonataMediaBundle\Provider\ProviderPool;
use MediaMonks\SonataMediaBundle\Provider\ProviderInterface;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;

class MediaAdmin extends AbstractAdmin
{
    /**
     * @var ProviderPool
     */
    private $providerPool;

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
                'updatedAt',
                'datetime'
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
         * @var Media $media
         */
        $media = $this->getSubject();
        if (!$media) {
            $media = $this->getNewInstance();
        }

        $provider = $this->getProvider($media);

        if ($media->getId()) {
            $provider->buildEditForm($formMapper);
        } else {
            $provider->buildCreateForm($formMapper);
        }
    }

    /**
     * @param MediaInterface $media
     */
    public function prePersist($media)
    {
        $this->getProvider($media)->prePersist($media);
    }

    /**
     * @param MediaInterface $media
     */
    public function preUpdate($media)
    {
        $this->getProvider($media)->preUpdate($media);
    }

    /**
     * @param $media
     * @return ProviderInterface
     */
    protected function getProvider(MediaInterface $media)
    {
        return $this->providerPool->getProvider($media->getProvider());
    }

    /**
     * @return MediaInterface
     */
    public function getNewInstance()
    {
        $media = parent::getNewInstance();
        if ($this->hasRequest()) {
            if ($this->getRequest()->isMethod('POST')) {
                $media->setProvider($this->getRequest()->get($this->getUniqid())['provider']);
            } elseif ($this->getRequest()->query->has('provider')) {
                $media->setProvider($this->getRequest()->query->get('provider'));
            } else {
                throw new \InvalidArgumentException('No provider was set');
            }
        }

        $provider = $this->getProvider($media);
        $media->setType($provider->getType());

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
        $collection->add('image', $this->getRouterIdParameter().'/image');
        $collection->add('download', $this->getRouterIdParameter().'/download');
    }

    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper->add('title');
    }
}
