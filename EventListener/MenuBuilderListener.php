<?php

namespace MediaMonks\SonataMediaBundle\EventListener;

use MediaMonks\SonataMediaBundle\Provider\ProviderPool;
use Sonata\AdminBundle\Event\ConfigureMenuEvent;

class MenuBuilderListener
{
    /**
     * @var ProviderPool
     */
    private $providerPool;

    /**
     * @param ProviderPool $providerPool
     */
    public function __construct(ProviderPool $providerPool)
    {
        $this->providerPool = $providerPool;
    }

    /**
     * @param ConfigureMenuEvent $event
     */
    public function addMenuItems(ConfigureMenuEvent $event)
    {
        $menu = $event->getMenu();

        $child = $menu->getChild('Media');

        foreach ($this->providerPool->getProviders() as $providerClass => $provider) {
            $providerChild = $child->addChild(
                'provider_'.spl_object_hash($provider),
                [
                    'route'           => 'admin_mediamonks_sonatamedia_media_create',
                    'routeParameters' => [
                        'provider' => $providerClass,
                    ],
                ]
            );
            $providerChild->setAttribute('icon', sprintf('<i class="%s" aria-hidden="true"></i>', $provider->getIcon()));
            $providerChild->setLabel('Add '.$provider->getTitle());
        }
    }
}
