<?php

declare(strict_types=1);

namespace Yaroslavche\SyliusPluginMarketplacePlugin;

use Composer\Factory;
use Composer\IO\NullIO;
use Exception;
use PhpParser\Error;
use PhpParser\Node;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\ArrayItem;
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Stmt;
use PhpParser\Node\Stmt\Return_;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitorAbstract;
use PhpParser\ParserFactory;
use PhpParser\PrettyPrinter\Standard;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Class PluginInstall
 * @package Yaroslavche\SyliusPluginMarketplacePlugin
 */
final class PluginInstall
{
    public const PLUGINS_DIR_NAME = 'plugins';

    /** @var PluginInterface $plugin */
    private $plugin;
    /** @var PluginUninstall $uninstall */
    private $uninstall;
    /** @var Filesystem $filesystem */
    private $filesystem;
    /** @var string $rootDir */
    private $rootDir;

    /**
     * PluginInstall constructor.
     * @param PluginInterface $plugin
     */
    public function __construct(PluginInterface $plugin)
    {
        $this->rootDir = '/home/yaroslav/projects/Sylius/SyliusMarketplacePlugin/tests/Application';
        $this->filesystem = new Filesystem();
        $this->plugin = $plugin;
        $this->uninstall = new PluginUninstall($plugin);
    }

    /**
     * Install plugin
     * @throws Exception
     */
    public function install(): void
    {
        try {
            $this->loadPackage();
            $this->importConfig();
            $this->registerBundle();
            $this->savePackageConfig();
            $this->installAssets();
            $this->uninstall->clearCache();
        } catch (Exception $exception) {
            $this->uninstall->uninstall();
            throw new Exception('Install failed' . $exception->getMessage());
        }
    }

    private function loadPackage()
    {
        $pluginsDirPath = sprintf('%s%s%s', $this->rootDir, DIRECTORY_SEPARATOR, self::PLUGINS_DIR_NAME);
        if (!$this->filesystem->exists($pluginsDirPath)) {
            $this->filesystem->mkdir($pluginsDirPath);
        }
        $composerJsonPath = sprintf('%s%scomposer.json', $pluginsDirPath, DIRECTORY_SEPARATOR);
        if (!$this->filesystem->exists($composerJsonPath)) {
            $fileResource = fopen($composerJsonPath, 'w+');
            fwrite($fileResource, '{}');
            fclose($fileResource);
        }
        $composer = Factory::create(new NullIO(), $composerJsonPath);
        $package = $composer->getRepositoryManager()->findPackage($this->plugin->getName(), '*');
        $targetDir = sprintf('%s%s%s', $pluginsDirPath, DIRECTORY_SEPARATOR, $this->plugin->getName());
        $composer->getDownloadManager()->download($package, $targetDir);
//        $this->updateLock();
    }

    private function importConfig(): void
    {
    }

    /**
     * Add entry to config/bundles.php return array
     * @throws Exception
     */
    private function registerBundle(): void
    {
        /** load AST */
        $bundlesFile = $this->rootDir . '/config/bundles.php';
        if (!$this->filesystem->exists($bundlesFile)) {
            throw new Exception('bundles.php file not found in config directory');
        }
        $code = file_get_contents($bundlesFile);
        $ast = $this->loadAST($code);

        /** check bundles array */
        if (!$ast[0] instanceof Return_ || !$ast[0]->expr instanceof Array_) {
            throw new Exception('Look\'s like you have custom bundles.php. Please install plugins manually.');
        }
        /** @var Array_ $bundlesArray */
        $bundlesArray = $ast[0]->expr;

        /** check registered */
        $pluginFQCN = $this->getPluginBundleClassName($this->plugin->getName());
        /** @var ArrayItem $arrayItem */
        foreach ($bundlesArray->items as $arrayItem) {
            if ($pluginFQCN === $this->getBundleClassName($arrayItem->key)) {
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
        $prettyPrinter = new Standard();
        $newCode = $prettyPrinter->prettyPrintFile($ast);
        file_put_contents($bundlesFile . '_test.php', $newCode);
    }

    /**
     * @param string $code
     * @return Stmt[]|null
     * @throws Exception
     */
    private function loadAST(string $code)
    {
        $parser = (new ParserFactory())->create(ParserFactory::PREFER_PHP7);
        try {
            $ast = $parser->parse($code);
        } catch (Error $error) {
            throw new Exception($error->getMessage(), $error->getCode(), $error);
        }
        return $ast;
    }

    /**
     * @param Expr|null $bundleClassExpr
     * @return string
     * @throws Exception
     */
    private function getBundleClassName(?Expr $bundleClassExpr): string
    {
        switch (get_class($bundleClassExpr)) {
            case ClassConstFetch::class:
                /** @var ClassConstFetch $bundleClassExpr */
                return implode('\\', $bundleClassExpr->class->parts);
            default:
                throw new Exception('Unhandled expression');
        }
    }

    private function savePackageConfig(): void
    {
    }

    private function installAssets(): void
    {
    }

    private function getPluginBundleClassName(string $name): string
    {
        throw new Exception('implement' . __METHOD__);
    }
}
