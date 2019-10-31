<?php

declare(strict_types=1);

namespace Yaroslavche\SyliusPluginMarketplacePlugin\PluginMarketplace;

use Exception;
use Yaroslavche\SyliusPluginMarketplacePlugin\Plugin\PluginInterface;
use Yaroslavche\SyliusPluginMarketplacePlugin\PluginRepository\PluginCollection;
use Yaroslavche\SyliusPluginMarketplacePlugin\PluginRepository\PluginRepositoryInterface;

/**
 * Interface PluginMarketplaceInterface
 * @package Yaroslavche\SyliusPluginMarketplacePlugin\PluginMarketplace
 */
interface PluginMarketplaceInterface
{
    /**
     * @param PluginInterface $plugin
     * @throws Exception
     */
    public function installPlugin(PluginInterface $plugin): void;

    /**
     * @param PluginInterface $plugin
     * @throws Exception
     */
    public function uninstallPlugin(PluginInterface $plugin): void;

    /**
     * @return PluginRepositoryInterface
     */
    public function getPluginRepository(): PluginRepositoryInterface;
}
