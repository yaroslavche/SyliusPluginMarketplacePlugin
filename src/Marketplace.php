<?php

declare(strict_types=1);

namespace Yaroslavche\SyliusPluginMarketplacePlugin;

/**
 * Class PluginMarketplaceService
 * @package Yaroslavche\SyliusPluginMarketplacePlugin\Service
 */
class Marketplace implements MarketplaceInterface
{
    /** @var PluginRepositoryInterface $pluginRepository */
    private $pluginRepository;

    /**
     * PluginMarketplaceService constructor.
     */
    public function __construct()
    {
        $this->pluginRepository = new PackagistPluginRepository();
    }

    /** @inheritDoc */
    public function list(?array $filter = null, ?array $sort = null, ?int $page = null): PluginCollection
    {
        return $this->pluginRepository->find();
    }

    /** @inheritDoc */
    public function installPlugin(PluginInterface $plugin): void
    {
        $plugin->install();
    }

    /** @inheritDoc */
    public function uninstallPlugin(PluginInterface $plugin): void
    {
        $plugin->uninstall();
    }

    /** @inheritDoc */
    public function getPluginRepository(): PluginRepositoryInterface
    {
        return $this->pluginRepository;
    }
}
