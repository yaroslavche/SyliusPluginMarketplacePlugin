<?php

declare(strict_types=1);

namespace Yaroslavche\SyliusPluginMarketplacePlugin;

use Exception;

/**
 * Class PluginInstall
 * @package Yaroslavche\SyliusPluginMarketplacePlugin
 */
final class PluginInstall
{
    /** @var PluginInterface $plugin */
    private $plugin;
    /** @var PluginUninstall $uninstall */
    private $uninstall;

    /**
     * PluginInstall constructor.
     * @param PluginInterface $plugin
     */
    public function __construct(PluginInterface $plugin)
    {
        $this->plugin = $plugin;
        $this->uninstall = new PluginUninstall($plugin);
    }

    /**
     * Install plugin
     */
    public function install()
    {
        try {
            $this->importConfig();
            $this->registerBundle();
            $this->savePackageConfig();
            $this->installAssets();
            $this->uninstall->clearCache();
        } catch (Exception $exception) {
            $this->uninstall->uninstall();
        }
    }

    private function importConfig(): void
    {
    }

    private function registerBundle(): void
    {
    }

    private function savePackageConfig(): void
    {
    }

    private function installAssets(): void
    {
    }
}
