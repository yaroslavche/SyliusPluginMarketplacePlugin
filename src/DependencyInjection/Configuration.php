<?php

declare(strict_types=1);

namespace Yaroslavche\SyliusMarketplacePlugin\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('yaroslavche_sylius_marketplace_plugin');
        if (\method_exists($treeBuilder, 'getRootNode')) {
            $rootNode = $treeBuilder->getRootNode();
        } else {
            // suppose to remove support versions using < 4.1
            // BC layer for symfony/config 4.1 and older
            $rootNode = $treeBuilder->root('yaroslavche_sylius_marketplace_plugin');
        }

        return $treeBuilder;
    }
}
