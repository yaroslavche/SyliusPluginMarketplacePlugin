<?php

declare(strict_types=1);

namespace Yaroslavche\SyliusPluginMarketplacePlugin\Listener;

use Sylius\Bundle\UiBundle\Menu\Event\MenuBuilderEvent;

/**
 * Class AdminMenuListener
 * @package Yaroslavche\SyliusPluginMarketplacePlugin\Listener
 */
final class AdminMenuListener
{
    /**
     * @param MenuBuilderEvent $event
     */
    public function addAdminMenuItems(MenuBuilderEvent $event): void
    {
        $menu = $event->getMenu();

        $configMenu = $menu->getChild('configuration');
        if (is_null($configMenu)) {
            return;
        }

        $configMenu
            ->addChild('plugin_marketplace', ['route' => 'yaroslavche_sylius_plugin_marketplace_plugin_index'])
            ->setLabel('Plugin Marketplace');
    }
}
