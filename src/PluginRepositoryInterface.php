<?php

declare(strict_types=1);

namespace Yaroslavche\SyliusMarketplacePlugin;

use Exception;

/**
 * Interface PluginRepositoryInterface
 * @package Yaroslavche\SyliusMarketplacePlugin
 */
interface PluginRepositoryInterface
{
    /**
     * @param string|null $name
     * @return PluginCollection
     * @throws Exception
     */
    public function find(?string $name = null): PluginCollection;
}
