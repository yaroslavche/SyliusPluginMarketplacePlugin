<?php

declare(strict_types=1);

namespace Yaroslavche\SyliusPluginMarketplacePlugin\PluginManager;

use Yaroslavche\SyliusPluginMarketplacePlugin\Plugin\PluginInterface;

/**
 * Interface PluginManagerInterface
 * @package Yaroslavche\SyliusPluginMarketplacePlugin\PluginManager
 */
interface PluginManagerInterface
{
    public function install(PluginInterface $plugin): void;

    public function uninstall(PluginInterface $plugin): void;

    public function loadPackage(PluginInterface $plugin): void;

    public function importRoutes(PluginInterface $plugin): void;

    public function importServices(PluginInterface $plugin): void;

    public function registerBundle(PluginInterface $plugin): void;

    public function writePluginConfig(PluginInterface $plugin): void;

    public function installAssets(PluginInterface $plugin): void;

    public function removeImportedRoutes(PluginInterface $plugin): void;

    public function removeImportedServices(PluginInterface $plugin): void;

    public function unregisterBundle(PluginInterface $plugin): void;

    public function removePluginConfig(PluginInterface $plugin): void;

    public function uninstallAssets(PluginInterface $plugin): void;

    public function removePackage(PluginInterface $plugin): void;

    public function clearCache(): void;

    public function updateLock(): void;
}
