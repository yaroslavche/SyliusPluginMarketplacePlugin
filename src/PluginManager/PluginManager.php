<?php

declare(strict_types=1);

namespace Yaroslavche\SyliusPluginMarketplacePlugin\PluginManager;

use Composer\Composer;
use Composer\Factory;
use Composer\IO\NullIO;
use Exception;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\ArrayItem;
use PhpParser\Node\Stmt\Return_;
use SplFileInfo;
use Symfony\Component\Yaml\Yaml;
use Yaroslavche\SyliusPluginMarketplacePlugin\Plugin\PluginInterface;
use Yaroslavche\SyliusPluginMarketplacePlugin\Service\FilesystemService;
use Yaroslavche\SyliusPluginMarketplacePlugin\Service\FinderService;
use Yaroslavche\SyliusPluginMarketplacePlugin\Service\PhpParserService;

/**
 * Class PluginManager
 * @package Yaroslavche\SyliusPluginMarketplacePlugin\PluginManager
 */
class PluginManager implements PluginManagerInterface
{
    public const PLUGINS_DIR_NAME = 'plugins';

    /** @var string $rootDir */
    private $rootDir;
    /** @var string $pluginsDir */
    private $pluginsDir;
    /** @var Composer $composer */
    private $composer;
    /** @var PhpParserService $phpParserService */
    private $phpParserService;
    /** @var FilesystemService $filesystemService */
    private $filesystemService;
    /** @var FinderService $finderService */
    private $finderService;

    /**
     * PluginManager constructor.
     * @param string $rootDir
     */
    public function __construct(string $rootDir)
    {
        $this->phpParserService = new PhpParserService();
        $this->filesystemService = new FilesystemService();
        $this->finderService = new FinderService();
        $this->rootDir = $rootDir;
        $this->pluginsDir = sprintf('%s%s%s', $this->rootDir, DIRECTORY_SEPARATOR, self::PLUGINS_DIR_NAME);
        $pluginsComposerJsonPath = sprintf('%s%scomposer.json', $this->pluginsDir, DIRECTORY_SEPARATOR);
        if (!$this->filesystemService->fileExists($pluginsComposerJsonPath)) {
            $this->filesystemService->saveFileContent($pluginsComposerJsonPath, '{}');
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
        $pluginResourceDir = sprintf(
            '%s%s%s%ssrc%sResources%sconfig',
            $this->pluginsDir,
            DIRECTORY_SEPARATOR,
            $plugin->getName(),
            DIRECTORY_SEPARATOR,
            DIRECTORY_SEPARATOR,
            DIRECTORY_SEPARATOR
        );
        $routesConfigPath = sprintf(
            '%s%sconfig%sroutes.yaml',
            $this->rootDir,
            DIRECTORY_SEPARATOR,
            DIRECTORY_SEPARATOR
        );
        $routesConfig = Yaml::parseFile($routesConfigPath);
        /** @var SplFileInfo $config */
        foreach ($this->finderService->findConfigs($pluginResourceDir) as $config) {
            if (!in_array($config->getExtension(), ['yml', 'yaml'])) {
                continue;
            }
            /*
            $configType = $this->guessConfigType($config);
            if ($configType === 'routes') {
                $routesConfig[str_replace(['/', '-'], '_', $plugin->getName())]['resource'] = $config->getFilename();
            }
            */
        }
        $this->filesystemService->saveFileContent($routesConfigPath, Yaml::dump($routesConfig));
    }

    /** @inheritDoc */
    public function importServices(PluginInterface $plugin): void
    {
        // TODO: Implement importServices() method.
    }

    private function getPluginSrcPath(PluginInterface $plugin): string
    {
        return sprintf('%s%s%s%ssrc', $this->pluginsDir, DIRECTORY_SEPARATOR, $plugin->getName(), DIRECTORY_SEPARATOR);
    }

    private function getPluginBundleFqcn(PluginInterface $plugin): string
    {
        /** @var SplFileInfo $pluginBundleFileInfo */
        $pluginBundleFileInfo = $this->finderService->findPluginBundleClass($this->getPluginSrcPath($plugin));
        $pluginBundleCode = $this->filesystemService->loadFileContent($pluginBundleFileInfo->getRealPath());
        $pluginBundleAst = $this->phpParserService->codeToAst($pluginBundleCode);

        return $this->phpParserService->getFqcnFromAst($pluginBundleAst);
    }

    /** @inheritDoc */
    public function registerBundle(PluginInterface $plugin): void
    {
        /** load bundles AST */
        $bundlesPhpFile = $this->rootDir . '/config/bundles.php';
        $bundlesCode = $this->filesystemService->loadFileContent($bundlesPhpFile);
        $bundlesAst = $this->phpParserService->codeToAst($bundlesCode);

        /** check bundles.php is default */
        if (!$bundlesAst[0] instanceof Return_ || !$bundlesAst[0]->expr instanceof Array_) {
            throw new Exception('Look\'s like you have custom bundles.php. Please install plugins manually.');
        }
        /** @var Array_ $bundlesArray */
        $bundlesArray = $bundlesAst[0]->expr;

        /** check registered */
        $pluginFqcn = $this->getPluginBundleFqcn($plugin);
        /** @var ArrayItem $arrayItem */
        foreach ($bundlesArray->items as $arrayItem) {
            if ($pluginFqcn === $this->phpParserService->getFqcnFromExpr($arrayItem->key)) {
                return;
            }
        }

        /** register */
        $this->phpParserService->insertBundle($bundlesAst, $pluginFqcn);

        /** save */
        $code = $this->phpParserService->astToCode($bundlesAst);
        $this->filesystemService->saveFileContent($bundlesPhpFile, $code);
    }

    /** @inheritDoc */
    public function writePluginConfig(PluginInterface $plugin): void
    {
        $pluginFqcn = $this->getPluginBundleFqcn($plugin);
        $namespaceParts = explode('\\', $pluginFqcn);
        array_pop($namespaceParts);
        $pluginBaseNamespace = implode('\\', $namespaceParts);

        $config = new BundleConfig($this->filesystemService, $this->finderService, $this->rootDir);
        $bundleConfig = $config->load($this->getPluginSrcPath($plugin), $pluginBaseNamespace);
        $this->filesystemService->saveFileContent($this->rootDir . '/config/bundle.yml', Yaml::dump($bundleConfig));
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
        $this->filesystemService->remove($pluginDir);
    }

    /** @inheritDoc */
    public function updateLock(): void
    {
        // TODO: Implement updateLock() method.
    }

    /**
     * @param SplFileInfo $configFile
     * @return string 'routes'|'services'
     * @throws Exception
     */
    private function guessConfigType(SplFileInfo $configFile): string
    {
        return 'routes';
    }
}
