<?php

namespace MediaMonks\SonataMediaBundle\Admin;

use InvalidArgumentException;
use MediaMonks\SonataMediaBundle\Model\AbstractMedia;
use MediaMonks\SonataMediaBundle\Model\MediaInterface;
use MediaMonks\SonataMediaBundle\Provider\ProviderInterface;
use MediaMonks\SonataMediaBundle\Provider\ProviderPool;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Route\RouteCollectionInterface;
use Sonata\Form\Validator\ErrorElement;

class MediaAdmin extends AbstractAdmin
{
    private ProviderPool $providerPool;
    private ?string $originalProviderReference;
    /** @inheritdoc */
    protected $baseRouteName = 'admin_mediamonks_sonatamedia_media';
    /** @inheritdoc */
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
     * @param ListMapper $list
     *
     * @return void
     */
    protected function configureListFields(ListMapper $list): void
    {
        $list
            ->addIdentifier('title')
            ->add('type')
            ->add('updatedAt')
            ->add('_action', 'actions', [
                'actions' => [
                    'edit' => [],
                    'delete' => [],
                ],
            ]);
    }

    /**
     * @param FormMapper $form
     *
     * @return void
     */
    protected function configureFormFields(FormMapper $form): void
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
            $provider->buildEditForm($form);
        } else {
            $provider->buildCreateForm($form);
        }
    }

    /**
     * @param object|AbstractMedia $object
     */
    public function prePersist(object $object): void
    {
        $this->getProvider($object)->update($object, true);
    }

    /**
     * @param object|AbstractMedia $object
     */
    public function preUpdate(object $object): void
    {
        $this->getProvider($object)->update($object, $this->isProviderReferenceUpdated($object));
    }

    /**
     * @param AbstractMedia $media
     *
     * @return bool
     */
    protected function isProviderReferenceUpdated(AbstractMedia $media): bool
    {
        return $this->originalProviderReference !== $media->getProviderReference();
    }

    /**
     * @param AbstractMedia $media
     *
     * @return ProviderInterface
     */
    protected function getProvider(AbstractMedia $media): ProviderInterface
    {
        if (empty($media->getProvider())) {
            throw new InvalidArgumentException('No provider was set');
        }

        $provider = $this->providerPool->getProvider($media->getProvider());
        $media->setType($provider->getType());

        return $this->providerPool->getProvider($media->getProvider());
    }

    /**
     * @return object|MediaInterface
     */
    protected function createNewInstance(): object
    {
        $media = parent::createNewInstance();
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
     * @param MediaInterface $object
     *
     * @return string
     */
    public function toString($object): string
    {
        return $object instanceof MediaInterface && $object->getTitle() !== null ? $object->getTitle() : 'Media';
    }

    /**
     * @param RouteCollection $collection
     */
    protected function configureRoutes(RouteCollectionInterface $collection): void
    {
        $collection->add('image', $this->getRouterIdParameter() . '/image/{width}/{height}');
        $collection->add('download', $this->getRouterIdParameter() . '/download');
    }

    /**
     * @param DatagridMapper $filter
     */
    protected function configureDatagridFilters(DatagridMapper $filter): void
    {
        $filter
            ->add('title')
            ->add('type')
            ->add('provider');
    }

    /**
     * @param ErrorElement $errorElement
     * @param object|AbstractMedia $object
     */
    public function validate(ErrorElement $errorElement, $object)
    {
        $this->getProvider($object)->validate($errorElement, $object);
    }

    /** @inheritDoc */
    protected function configure(): void
    {
        $this->setTemplate('outer_list_rows_mosaic', '@MediaMonksSonataMedia/CRUD/outer_list_rows_mosaic.html.twig');
        $this->setTemplate('edit', '@MediaMonksSonataMedia/CRUD/edit.html.twig');
    }
}
