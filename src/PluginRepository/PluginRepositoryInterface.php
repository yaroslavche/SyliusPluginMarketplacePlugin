<?php

declare(strict_types=1);

namespace Yaroslavche\SyliusPluginMarketplacePlugin\PluginRepository;

use Exception;

/**
 * Interface PluginRepositoryInterface
 * @package Yaroslavche\SyliusPluginMarketplacePlugin\PluginRepository
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
