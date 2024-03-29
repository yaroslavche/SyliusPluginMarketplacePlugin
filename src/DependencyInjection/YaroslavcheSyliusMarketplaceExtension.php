<?php

declare(strict_types=1);

namespace Yaroslavche\SyliusPluginMarketplacePlugin\DependencyInjection;

use Exception;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

/**
 * Class YaroslavcheSyliusMarketplaceExtension
 * @package Yaroslavche\SyliusPluginMarketplacePlugin\DependencyInjection
 */
final class YaroslavcheSyliusMarketplaceExtension extends Extension
{
    /**
     * {@inheritdoc}
     * @throws Exception
     */
    public function load(array $config, ContainerBuilder $container): void
    {
        $configuration = $this->getConfiguration([], $container);
        if (null === $configuration) {
            return;
        }
        $this->processConfiguration($configuration, $config);

        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yml');
    }
}
