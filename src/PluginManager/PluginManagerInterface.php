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
    /**
     * @param PluginInterface $plugin
     */
    public function install(PluginInterface $plugin): void;

    /**
     * @param PluginInterface $plugin
     */
    public function uninstall(PluginInterface $plugin): void;

    /**
     * @param PluginInterface $plugin
     */
    public function loadPackage(PluginInterface $plugin): void;

    /**
     * @param PluginInterface $plugin
     */
    public function importRoutes(PluginInterface $plugin): void;

    /**
     * @param PluginInterface $plugin
     */
    public function importServices(PluginInterface $plugin): void;

    /**
     * @param PluginInterface $plugin
     */
    public function registerBundle(PluginInterface $plugin): void;

    /**
     * @param PluginInterface $plugin
     */
    public function writePluginConfig(PluginInterface $plugin): void;

    /**
     * @param PluginInterface $plugin
     */
    public function installAssets(PluginInterface $plugin): void;

    /**
     * @param PluginInterface $plugin
     */
    public function removeImportedRoutes(PluginInterface $plugin): void;

    /**
     * @param PluginInterface $plugin
     */
    public function removeImportedServices(PluginInterface $plugin): void;

    /**
     * @param PluginInterface $plugin
     */
    public function unregisterBundle(PluginInterface $plugin): void;

    /**
     * @param PluginInterface $plugin
     */
    public function removePluginConfig(PluginInterface $plugin): void;

    /**
     * @param PluginInterface $plugin
     */
    public function uninstallAssets(PluginInterface $plugin): void;

    /**
     * @param PluginInterface $plugin
     */
    public function removePackage(PluginInterface $plugin): void;

    /**
     *
     */
    public function clearCache(): void;

    /**
     *
     */
    public function updateLock(): void;
}
