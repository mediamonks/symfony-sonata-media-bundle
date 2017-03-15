<?php

namespace MediaMonks\SonataMediaBundle\EventListener;

use MediaMonks\SonataMediaBundle\Provider\ProviderPool;
use Sonata\AdminBundle\Event\ConfigureMenuEvent;
use Symfony\Component\Translation\TranslatorInterface;

class MenuBuilderListener
{
    /**
     * @var ProviderPool
     */
    private $providerPool;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @param ProviderPool $providerPool
     * @param TranslatorInterface $translator
     */
    public function __construct(
        ProviderPool $providerPool,
        TranslatorInterface $translator
    ) {
        $this->providerPool = $providerPool;
        $this->translator = $translator;
    }

    /**
     * @param ConfigureMenuEvent $event
     */
    public function addMenuItems(ConfigureMenuEvent $event)
    {
        $menu = $event->getMenu();

        $child = $menu->getChild($this->translator->trans('menu.title'));

        foreach ($this->providerPool->getProviders(
        ) as $providerClass => $provider) {
            $providerChild = $child->addChild(
                'provider_'.spl_object_hash($provider),
                [
                    'route' => 'admin_mediamonks_sonatamedia_media_create',
                    'routeParameters' => [
                        'provider' => $providerClass,
                    ],
                ]
            );
            $providerChild->setAttribute(
                'icon',
                sprintf(
                    '<i class="%s" aria-hidden="true"></i>',
                    $provider->getIcon()
                )
            );
            $providerChild->setLabel(
                $this->translator->trans(
                    'menu.provider',
                    [
                        '%provider%' => $this->translator->trans($provider->getName())
                    ]
                )
            );
        }
    }
}
