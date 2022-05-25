<?php

namespace MediaMonks\SonataMediaBundle\EventListener;

use Knp\Menu\ItemInterface;
use MediaMonks\SonataMediaBundle\Provider\ProviderPool;
use Sonata\AdminBundle\Event\ConfigureMenuEvent;
use Symfony\Contracts\Translation\TranslatorInterface;

class MenuBuilderListener
{
    const DEFAULT_CREATE_ROUTE = 'admin_mediamonks_sonatamedia_media_create';

    private ProviderPool $providerPool;
    private TranslatorInterface $translator;
    private string $route;

    /**
     * @param ProviderPool $providerPool
     * @param TranslatorInterface $translator
     * @param string|null $route
     */
    public function __construct(
        ProviderPool $providerPool,
        TranslatorInterface $translator,
        ?string $route = null
    )
    {
        $this->providerPool = $providerPool;
        $this->translator = $translator;
        $this->route = $route ?? static::DEFAULT_CREATE_ROUTE;
    }

    /**
     * @param ConfigureMenuEvent $event
     */
    public function addMenuItems(ConfigureMenuEvent $event): void
    {
        $menu = $event->getMenu();

        $child = $menu->getChild('Media');
        if (empty($child)) {
            return;
        }
        $child->setLabel($this->translator->trans('menu.title'));

        foreach ($this->providerPool->getProviders() as $providerClass => $provider) {
            $this->addProviderMenuChild(
                $child,
                $providerClass,
                $this->route,
                ['provider' => $providerClass],
                $provider->getName(),
                $provider->getIcon()
            );
        }
    }

    /**
     * @param ItemInterface $menu
     * @param ItemInterface|string $childRef
     * @param string $route
     * @param array $routeParameters
     * @param string|null $label
     * @param string|null $icon
     */
    private function addProviderMenuChild(ItemInterface $menu, $childRef, string $route, array $routeParameters = [], ?string $label = null, ?string $icon = null): void
    {
        $child = $menu->addChild($childRef, ['route' => $route, 'routeParameters' => $routeParameters]);
        $label = $label ?? (string)($childRef instanceof ItemInterface ? $childRef->getLabel() : $childRef);
        $child->setLabel($this->translator->trans('menu.provider', ['%provider%' => $this->translator->trans($label)]));
        if (!empty($icon)) {
            $child->setAttribute('icon', sprintf('<i class="%s" aria-hidden="true"></i>', $icon));
        }
    }
}
