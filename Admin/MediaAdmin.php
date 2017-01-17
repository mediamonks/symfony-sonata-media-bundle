<?php

namespace MediaMonks\SonataMediaBundle\Admin;

use MediaMonks\SonataMediaBundle\Entity\Media;
use MediaMonks\SonataMediaBundle\Model\MediaInterface;
use MediaMonks\SonataMediaBundle\Provider\ProviderPool;
use MediaMonks\SonataMediaBundle\Provider\ProviderInterface;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Component\Validator\Constraints as Constraint;

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
            ->add('type', null, [
                'template' => 'MediaMonksSonataMediaBundle:MediaAdmin:list_type.html.twig'
            ])
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
        return $this->providerPool->getProvider($media->getProviderName());
    }

    /**
     * @return MediaInterface
     */
    public function getNewInstance()
    {
        $media = parent::getNewInstance();
        if ($this->hasRequest()) {
            if ($this->getRequest()->isMethod('POST')) {
                $media->setProviderName($this->getRequest()->get($this->getUniqid())['providerName']);
            } elseif ($this->getRequest()->query->has('provider')) {
                $media->setProviderName($this->getRequest()->query->get('provider'));
            } else {
                $media->setProviderName('mediamonks.media.provider.image'); // @todo load default provider from config
            }
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
}
