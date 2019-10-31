<?php

declare(strict_types=1);

namespace Yaroslavche\SyliusPluginMarketplacePlugin\PluginManager;

use Composer\Composer;
use Composer\Factory;
use Composer\IO\NullIO;
use Exception;
use PhpParser\Node;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\ArrayItem;
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Stmt\Return_;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitorAbstract;
use SplFileInfo;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Yaroslavche\SyliusPluginMarketplacePlugin\Plugin\PluginInterface;
use Yaroslavche\SyliusPluginMarketplacePlugin\Service\PhpParserService;

/**
 * Class PluginManager
 * @package Yaroslavche\SyliusPluginMarketplacePlugin\PluginManager
 */
class PluginManager implements PluginManagerInterface
{
    public const PLUGINS_DIR_NAME = 'plugins';

    /** @var Filesystem $filesystem */
    private $filesystem;
    /** @var Finder $finder */
    private $finder;
    /** @var string $rootDir */
    private $rootDir;
    /** @var string $pluginsDir */
    private $pluginsDir;
    /** @var Composer $composer */
    private $composer;
    /** @var PhpParserService $phpParserService */
    private $phpParserService;

    /**
     * PluginManager constructor.
     */
    public function __construct()
    {
        $this->phpParserService = new PhpParserService();
        $this->filesystem = new Filesystem();
        $this->finder = new Finder();
        $this->rootDir = '/home/yaroslav/projects/Sylius/SyliusMarketplacePlugin/tests/Application';
        $this->pluginsDir = sprintf('%s%s%s', $this->rootDir, DIRECTORY_SEPARATOR, self::PLUGINS_DIR_NAME);
        $pluginsComposerJsonPath = sprintf('%s%scomposer.json', $this->pluginsDir, DIRECTORY_SEPARATOR);
        if (!$this->filesystem->exists($pluginsComposerJsonPath)) {
            $this->filesystem->appendToFile($pluginsComposerJsonPath, '{}');
        }
        $this->composer = Factory::create(new NullIO(), $pluginsComposerJsonPath);
    }


    /** @inheritDoc */
    public function install(PluginInterface $plugin): void
    {
        try {
            $this->loadPackage($plugin);
            $this->registerBundle($plugin);
            $this->importRoutes($plugin);
            $this->importServices($plugin);
            $this->writePluginConfig($plugin);
            $this->installAssets($plugin);
            $this->clearCache();
        } catch (Exception $exception) {
            $this->uninstall($plugin);
            throw new Exception($exception->getMessage(), $exception->getCode(), $exception);
        }
    }

    /** @inheritDoc */
    public function uninstall(PluginInterface $plugin): void
    {
        $this->removeImportedRoutes($plugin);
        $this->removeImportedServices($plugin);
        $this->unregisterBundle($plugin);
        $this->removePluginConfig($plugin);
        $this->uninstallAssets($plugin);
        $this->removePackage($plugin);
        $this->clearCache();
    }

    /** @inheritDoc */
    public function loadPackage(PluginInterface $plugin): void
    {
        $package = $this->composer->getRepositoryManager()->findPackage($plugin->getName(), '*');
        $pluginDir = sprintf('%s%s%s', $this->pluginsDir, DIRECTORY_SEPARATOR, $plugin->getName());
        $this->composer->getDownloadManager()->download($package, $pluginDir);
        $this->updateLock();
    }

    /** @inheritDoc */
    public function importRoutes(PluginInterface $plugin): void
    {
        // get from autoload?
        $pluginSrcDir = sprintf(
            '%s%s%s%ssrc',
            $this->pluginsDir,
            DIRECTORY_SEPARATOR,
            $plugin->getName(),
            DIRECTORY_SEPARATOR
        );
        $pluginClassFileFinder = $this->finder->in($pluginSrcDir)->name('*Sylius*Plugin.php');
        $iterator = $pluginClassFileFinder->getIterator();
        $iterator->rewind();
        /** @var SplFileInfo $pluginClassFile */
        $pluginClassFile = $iterator->current();
//        dd(substr($pluginClassFile->getFilename(), 0, -4));
    }

    /** @inheritDoc */
    public function importServices(PluginInterface $plugin): void
    {
        // TODO: Implement importServices() method.
    }

    /** @inheritDoc */
    public function registerBundle(PluginInterface $plugin): void
    {
        /** load AST */
        $bundlesFile = $this->rootDir . '/config/bundles.php';
        if (!$this->filesystem->exists($bundlesFile)) {
            throw new Exception('bundles.php file not found in config directory');
        }
        $code = file_get_contents($bundlesFile);
        $ast = $this->phpParserService->loadAst($code);

        /** check bundles array */
        if (!$ast[0] instanceof Return_ || !$ast[0]->expr instanceof Array_) {
            throw new Exception('Look\'s like you have custom bundles.php. Please install plugins manually.');
        }
        /** @var Array_ $bundlesArray */
        $bundlesArray = $ast[0]->expr;

        /** check registered */
        $pluginFQCN = $this->getPluginBundleClassName($plugin);
        /** @var ArrayItem $arrayItem */
        foreach ($bundlesArray->items as $arrayItem) {
            if ($pluginFQCN === $this->getBundleClassNameFromExpr($arrayItem->key)) {
                return;
            }
        }

        /** register */
        $traverser = new NodeTraverser();
        $visitor = new class ($pluginFQCN) extends NodeVisitorAbstract {
            /** @var string $pluginFQCN */
            private $pluginFQCN;

            /**
             *  constructor.
             * @param string $pluginFQCN
             */
            public function __construct(string $pluginFQCN)
            {
                $this->pluginFQCN = $pluginFQCN;
            }

            /** @inheritDoc */
            public function enterNode(Node $node)
            {
                if ($node instanceof Array_) {
                    $key = new ClassConstFetch(
                        $this->pluginFQCN,
                        $this->pluginFQCN
                    );
//                    $node->items[] = new ArrayItem($key);
                }
            }
        };
        $traverser->addVisitor($visitor);
        $ast = $traverser->traverse($ast);
        $this->filesystem->appendToFile($bundlesFile . '_test.php', $this->phpParserService->astToCode($ast));
    }

    /** @inheritDoc */
    public function writePluginConfig(PluginInterface $plugin): void
    {
        // TODO: Implement writePluginConfig() method.
    }

    /** @inheritDoc */
    public function installAssets(PluginInterface $plugin): void
    {
        // TODO: Implement installAssets() method.
    }

    /** @inheritDoc */
    public function removeImportedRoutes(PluginInterface $plugin): void
    {
        // TODO: Implement removeImportedRoutes() method.
    }

    /** @inheritDoc */
    public function removeImportedServices(PluginInterface $plugin): void
    {
        // TODO: Implement removeImportedServices() method.
    }

    /** @inheritDoc */
    public function unregisterBundle(PluginInterface $plugin): void
    {
        // TODO: Implement unregisterBundle() method.
    }

    /** @inheritDoc */
    public function removePluginConfig(PluginInterface $plugin): void
    {
        // TODO: Implement removePluginConfig() method.
    }

    /** @inheritDoc */
    public function uninstallAssets(PluginInterface $plugin): void
    {
        // TODO: Implement uninstallAssets() method.
    }

    /** @inheritDoc */
    public function clearCache(): void
    {
        // TODO: Implement clearCache() method.
    }

    /** @inheritDoc */
    public function removePackage(PluginInterface $plugin): void
    {
        $pluginDir = sprintf('%s%s%s', $this->pluginsDir, DIRECTORY_SEPARATOR, $plugin->getName());
        $this->filesystem->remove($pluginDir);
    }

    public function updateLock(): void
    {
        // TODO: Implement updateLock() method.
    }

    /**
     * @param PluginInterface $plugin
     * @return string
     */
    private function getPluginBundleClassName(PluginInterface $plugin): string
    {
        return $plugin->getName();
    }

    /**
     * @param Expr $key
     * @return string
     * @throws Exception
     */
    private function getBundleClassNameFromExpr(Expr $key): string
    {
        switch (get_class($key)) {
            case ClassConstFetch::class:
                /** @var ClassConstFetch $key */
                return implode('\\', $key->class->parts);
            default:
                throw new Exception('Unhandled bundle classname expression');
        }
    }
}
