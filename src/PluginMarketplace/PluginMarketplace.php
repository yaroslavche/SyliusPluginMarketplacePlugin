<?php

declare(strict_types=1);

namespace Yaroslavche\SyliusPluginMarketplacePlugin\PluginMarketplace;

use Yaroslavche\SyliusPluginMarketplacePlugin\Plugin\PluginInterface;
use Yaroslavche\SyliusPluginMarketplacePlugin\PluginManager\PluginManager;
use Yaroslavche\SyliusPluginMarketplacePlugin\PluginRepository\PackagistPluginRepository;
use Yaroslavche\SyliusPluginMarketplacePlugin\PluginRepository\PluginRepositoryInterface;

/**
 * Class PluginMarketplace
 * @package Yaroslavche\SyliusPluginMarketplacePlugin\PluginMarketplace
 */
class PluginMarketplace implements PluginMarketplaceInterface
{
    /** @var PluginRepositoryInterface $pluginRepository */
    private $pluginRepository;
    /** @var PluginManager $pluginManager */
    private $pluginManager;

    /**
     * PluginMarketplaceService constructor.
     */
    public function __construct()
    {
        $this->pluginRepository = new PackagistPluginRepository();
        $this->pluginManager = new PluginManager();
    }

    /** @inheritDoc */
    public function installPlugin(PluginInterface $plugin): void
    {
        $this->pluginManager->install($plugin);
    }

    /** @inheritDoc */
    public function uninstallPlugin(PluginInterface $plugin): void
    {
        $this->pluginManager->uninstall($plugin);
    }

    /** @inheritDoc */
    public function getPluginRepository(): PluginRepositoryInterface
    {
        return $this->pluginRepository;
    }
}
