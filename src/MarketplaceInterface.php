<?php

declare(strict_types=1);

namespace Yaroslavche\SyliusPluginMarketplacePlugin;

use Exception;

/**
 * Interface PluginMarketplaceInterface
 * @package Yaroslavche\SyliusPluginMarketplacePlugin
 */
interface MarketplaceInterface
{
    /**
     * @param array<string, string>|null $filter
     * @param array<string, string>|null $sort
     * @param int|null $page
     * @return PluginCollection
     * @throws Exception
     */
    public function list(?array $filter = null, ?array $sort = null, ?int $page = null): PluginCollection;

    /**
     * @param PluginInterface $plugin
     */
    public function installPlugin(PluginInterface $plugin): void;

    /**
     * @param PluginInterface $plugin
     */
    public function uninstallPlugin(PluginInterface $plugin): void;

    /**
     * @return PluginRepositoryInterface
     */
    public function getPluginRepository(): PluginRepositoryInterface;
}
