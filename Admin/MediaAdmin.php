<?php

namespace MediaMonks\MediaBundle\Admin;

use MediaMonks\MediaBundle\Entity\Media;
use MediaMonks\MediaBundle\Provider\Pool;
use MediaMonks\MediaBundle\Provider\ProviderInterface;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Component\Validator\Constraints as Constraint;

class MediaAdmin extends AbstractAdmin
{
    /**
     * @var Pool
     */
    private $pool;

    /**
     * MediaAdmin constructor.
     * @param string $code
     * @param string $class
     * @param string $baseControllerName
     * @param Pool $pool
     */
    public function __construct($code, $class, $baseControllerName, Pool $pool)
    {
        parent::__construct($code, $class, $baseControllerName);
        $this->baseControllerName = $baseControllerName;
        $this->pool = $pool;
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('title')
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
     * {@inheritdoc}
     */
    public function prePersist($media)
    {
        $this->getProvider($media)->prePersist($media);
    }

    /**
     * {@inheritdoc}
     */
    public function preUpdate($media)
    {
        $this->getProvider($media)->preUpdate($media);
    }

    /**
     * @param $media
     * @return ProviderInterface
     */
    protected function getProvider($media)
    {
        return $this->pool->getProvider($media->getProviderName());
    }

    /**
     * {@inheritdoc}
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
                $media->setProviderName('mediamonks.media.provider.image'); // @todo default provider in config
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
        return $object instanceof MediaAdmin
            ? $object->getTitle()
            : 'Media';
    }
}
