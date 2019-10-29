<?php

declare(strict_types=1);

namespace Yaroslavche\SyliusPluginMarketplacePlugin;

/**
 * Class PluginUninstall
 * @package Yaroslavche\SyliusPluginMarketplacePlugin
 */
final class PluginUninstall
{
    /** @var PluginInterface $plugin */
    private $plugin;

    /**
     * PluginInstall constructor.
     * @param PluginInterface $plugin
     */
    public function __construct(PluginInterface $plugin)
    {
        $this->plugin = $plugin;
    }

    /**
     * Uninstall plugin
     */
    public function uninstall(): void
    {
        $this->removeImportedConfigs();
        $this->unregisterBundle();
        $this->removePackageConfig();
        $this->uninstallAssets();
        $this->clearCache();
    }

    /**
     * Remove imported Resources/config/*.*
     */
    private function removeImportedConfigs(): void
    {
    }

    /**
     * Remove entry from config/bundles.php
     */
    private function unregisterBundle(): void
    {
    }

    /**
     * Remove config/packages/{plugin}.yml
     */
    private function removePackageConfig(): void
    {
    }

    /**
     * Remove plugin assets
     */
    private function uninstallAssets(): void
    {
    }

    /**
     * Clear cache
     */
    public function clearCache(): void
    {
    }
}
