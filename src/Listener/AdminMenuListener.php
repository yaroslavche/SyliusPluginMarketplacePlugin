<?php

declare(strict_types=1);

namespace Yaroslavche\SyliusMarketplacePlugin\Listener;

use Sylius\Bundle\UiBundle\Menu\Event\MenuBuilderEvent;

/**
 * Class AdminMenuListener
 * @package Yaroslavche\SyliusMarketplacePlugin\Listener
 */
final class AdminMenuListener
{
    /**
     * @param MenuBuilderEvent $event
     */
    public function addAdminMenuItems(MenuBuilderEvent $event): void
    {
        $menu = $event->getMenu();

        $menu
            ->getChild('configuration')
            ->addChild('plugin_marketplace', ['route' => 'yaroslavche_sylius_marketplace_plugin_index'])
            ->setLabel('Plugin Marketplace');
    }
}
