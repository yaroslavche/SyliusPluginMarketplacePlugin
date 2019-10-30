<?php

declare(strict_types=1);

namespace Yaroslavche\SyliusPluginMarketplacePlugin;

use Exception;

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
     * @throws Exception
     */
    public function uninstall(): void
    {
        try {
            $this->removeImportedConfigs();
            $this->unregisterBundle();
            $this->removePackageConfig();
            $this->uninstallAssets();
            $this->clearCache();
        } catch (Exception $exception) {
            throw new Exception('Uninstall failed');
        }
    }

    /**
     * Remove imported Resources/config/*.*
     */
    private function removeImportedConfigs(): void
    {
    }

    /**
     * Remove entry from config/bundles.php return array
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
