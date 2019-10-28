<?php

declare(strict_types=1);

namespace Yaroslavche\SyliusMarketplacePlugin;

/**
 * Interface PluginRepositoryInterface
 * @package Yaroslavche\SyliusMarketplacePlugin
 */
interface PluginRepositoryInterface
{
    /**
     * @param string|null $name
     * @return PluginCollection
     */
    public function find(?string $name = null): PluginCollection;
}
