<?php

declare(strict_types=1);

namespace Yaroslavche\SyliusPluginMarketplacePlugin;

/**
 * Class PluginMarketplace
 * @package Yaroslavche\SyliusPluginMarketplacePlugin
 */
class PluginMarketplace implements PluginMarketplaceInterface
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
        (new PluginManager())->install($plugin);
    }

    /** @inheritDoc */
    public function uninstallPlugin(PluginInterface $plugin): void
    {
        (new PluginManager())->uninstall($plugin);
    }

    /** @inheritDoc */
    public function getPluginRepository(): PluginRepositoryInterface
    {
        return $this->pluginRepository;
    }
}
