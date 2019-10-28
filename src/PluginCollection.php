<?php

declare(strict_types=1);

namespace Yaroslavche\SyliusMarketplacePlugin;

use ArrayIterator;
use ArrayObject;
use Exception;
use IteratorAggregate;

/**
 * Class PluginCollection
 * @package Yaroslavche\SyliusMarketplacePlugin
 */
class PluginCollection implements IteratorAggregate
{
    /** @var array<string, PluginInterface> $plugins */
    private $plugins;

    /**
     * PluginCollection constructor.
     */
    public function __construct()
    {
        $this->plugins = [];
    }

    /**
     * @param PluginInterface $plugin
     * @throws Exception
     */
    public function add(PluginInterface $plugin): void
    {
        if (array_key_exists($plugin->getName(), $this->plugins)) {
            throw new Exception(sprintf('Plugin %s already exists', $plugin->getName()));
        }
        $this->plugins[$plugin->getName()] = $plugin;
    }

    /**
     * @param string $name
     * @return PluginInterface
     * @throws Exception
     */
    public function get(string $name): PluginInterface
    {
        if (!array_key_exists($name, $this->plugins)) {
            throw new Exception(sprintf('Plugin %s not found', $name));
        }
        return $this->plugins[$name];
    }

    /**
     * @return ArrayIterator
     */
    public function getIterator(): ArrayIterator
    {
        return (new ArrayObject($this->plugins))->getIterator();
    }
}
