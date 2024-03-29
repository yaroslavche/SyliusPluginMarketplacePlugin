<?php

declare(strict_types=1);

namespace Yaroslavche\SyliusPluginMarketplacePlugin\Service;

use SplFileInfo;
use Symfony\Component\Finder\Finder;

/**
 * Class FinderService
 * @package Yaroslavche\SyliusPluginMarketplacePlugin\Service
 */
class FinderService
{
    /** @var Finder $finder */
    private $finder;

    /**
     * FinderService constructor.
     */
    public function __construct()
    {
        $this->finder = new Finder();
    }

    /**
     * @param string $pluginSrcDir
     * @return SplFileInfo
     */
    public function findPluginBundleClass(string $pluginSrcDir): SplFileInfo
    {
        $pluginClassFileFinder = $this->finder->files()->in($pluginSrcDir)->name('*Plugin.php');
        $iterator = $pluginClassFileFinder->getIterator();
        $iterator->rewind();
        return $iterator->current();
    }

    public function findConfigs(string $pluginResourceDir): \Generator
    {
        $pluginClassFileFinder = $this->finder->files()->in($pluginResourceDir)/*->name(['*.yml', '*.yaml'])*/;
        foreach ($pluginClassFileFinder->files() as $file) {
            yield $file;
        }
    }
}
