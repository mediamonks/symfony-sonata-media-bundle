<?php

namespace MediaMonks\SonataMediaBundle\EventListener;

use Knp\Menu\ItemInterface;
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

        $child = $menu->getChild('Media');
        $child->setLabel($this->translator->trans('menu.title'));

        /*$this->addProviderMenuChild(
            $child,
            'batch',
            'mediamonks_media_batch',
            [],
            'batch',
            'fa fa-magic'
        );*/

        foreach ($this->providerPool->getProviders() as $providerClass => $provider) {
            $this->addProviderMenuChild(
                $child,
                'provider_'.spl_object_hash($provider),
                'admin_mediamonks_sonatamedia_media_create',
                ['provider' => $providerClass],
                $provider->getName(),
                $provider->getIcon()
            );
        }
    }

    /**
     * @param ItemInterface $menu
     * @param $route
     * @param $routeParameters
     * @param $label
     * @param $icon
     */
    private function addProviderMenuChild($menu, $name, $route, $routeParameters, $label, $icon)
    {
        $child = $menu->addChild(
            $name,
            [
                'route' => $route,
                'routeParameters' => $routeParameters,
            ]
        );
        $child->setLabel($this->translator->trans(
            'menu.provider',
            [
                '%provider%' => $this->translator->trans($label),
            ]
        ));
        $child->setAttribute(
            'icon',
            sprintf('<i class="%s" aria-hidden="true"></i>', $icon)
        );

    }
}
