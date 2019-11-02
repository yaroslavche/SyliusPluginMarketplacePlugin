<?php

declare(strict_types=1);

namespace Yaroslavche\SyliusPluginMarketplacePlugin\PluginManager;

use Closure;
use Exception;
use LogicException;
use ReflectionClass;
use ReflectionException;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\BooleanNodeDefinition;
use Symfony\Component\Config\Definition\Builder\EnumNodeDefinition;
use Symfony\Component\Config\Definition\Builder\FloatNodeDefinition;
use Symfony\Component\Config\Definition\Builder\IntegerNodeDefinition;
use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Symfony\Component\Config\Definition\Builder\ScalarNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\Builder\VariableNodeDefinition;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Yaroslavche\SyliusPluginMarketplacePlugin\Service\FilesystemService;
use Yaroslavche\SyliusPluginMarketplacePlugin\Service\FinderService;

/**
 * Class BundleConfig
 * @package Yaroslavche\SyliusPluginMarketplacePlugin\PluginManager
 */
class BundleConfig
{

    public const NODE_TYPE_SCALAR = 'scalar';
    public const NODE_TYPE_VARIABLE = 'variable';
    public const NODE_TYPE_BOOLEAN = 'boolean';
    public const NODE_TYPE_ARRAY = 'array';
    public const NODE_TYPE_INTEGER = 'integer';
    public const NODE_TYPE_FLOAT = 'float';
    public const NODE_TYPE_ENUM = 'enum';

    /** @var FilesystemService $filesystemService */
    private $filesystemService;
    /** @var string $rootDir */
    private $rootDir;
    /** @var FinderService $finderService */
    private $finderService;

    /**
     * BundleConfig constructor.
     * @param FilesystemService $filesystemService
     * @param FinderService $finderService
     * @param string $rootDir
     */
    public function __construct(FilesystemService $filesystemService, FinderService $finderService, string $rootDir)
    {
        $this->filesystemService = $filesystemService;
        $this->finderService = $finderService;
        $this->rootDir = $rootDir;
    }

    /**
     * @param string $pluginSrcDir
     * @param string $pluginBaseNamespace
     * @return array
     * @throws ReflectionException
     * @throws Exception
     */
    public function load(string $pluginSrcDir, string $pluginBaseNamespace): array
    {
        $configurationClass = sprintf(
            '%s%sDependencyInjection%sConfiguration.php',
            $pluginSrcDir,
            DIRECTORY_SEPARATOR,
            DIRECTORY_SEPARATOR
        );
        if (!$this->filesystemService->fileExists($configurationClass)) {
            throw new Exception('Configuration not found.');
        }
        require_once $configurationClass;
        $pluginConfigFqcn = $pluginBaseNamespace . '\\DependencyInjection\\Configuration';
        $configuration = new ReflectionClass($pluginConfigFqcn);

        /** @var ConfigurationInterface $configurationInstance */
        $configurationInstance = new $pluginConfigFqcn(false);
        /** @var TreeBuilder $treeBuilder */
        $treeBuilder = $configuration->getMethod('getConfigTreeBuilder')->invoke($configurationInstance);
        $treeBuilder->buildTree();

        /** @var ArrayNodeDefinition $rootNode */
        $rootNode = $treeBuilder->getRootNode();
        $definitions = [];
        foreach ($rootNode->getChildNodeDefinitions() as $childName => $childDefinition) {
            $definitions[$childName] = $this->handleDefinition($childDefinition);
        }
        return $definitions;
    }

    /**
     * @param NodeDefinition $definition
     * @return array[]
     */
    private function handleDefinition(NodeDefinition $definition): array
    {
        $definitionClosure = function (NodeDefinition $nodeDefinition, array $fields) {
            $definition = [];
            foreach ($fields as $key => $field) {
                $definition[$field] = $nodeDefinition->{$field};
            }
            return $definition;
        };
        $definitionClosure = Closure::bind($definitionClosure, null, $definition);
        $definitionDataArray = $definitionClosure($definition, $this->getNodeDefinitionFields($definition));
        $definitionDataArray['type'] = $this->getNodeType($definition);
        $prototypeField = $definitionDataArray['prototype'] ?? null;
        if ($prototypeField instanceof NodeDefinition) {
            $definitionDataArray['prototype'] = $this->getNodeType($prototypeField);
        }
        foreach ($definitionDataArray['children'] ?? [] as $name => $childDefinition) {
            $definitionDataArray['children'][$name] = $this->handleDefinition($childDefinition);
        }
        return $definitionDataArray;
    }

    /**
     * @param NodeDefinition $nodeDefinition
     * @return string[]
     */
    private function getNodeDefinitionFields(NodeDefinition $nodeDefinition): array
    {
        $fields = [
            'name', 'normalization', 'validation', 'defaultValue', 'default', 'required', 'deprecationMessage',
            'merge', 'allowEmptyValue', 'nullEquivalent', 'trueEquivalent', 'falseEquivalent', 'pathSeparator',
            'parent', 'attributes'
        ];
        switch ($this->getNodeType($nodeDefinition)) {
            case self::NODE_TYPE_BOOLEAN:
            case self::NODE_TYPE_VARIABLE:
            case self::NODE_TYPE_SCALAR:
                break;
            case self::NODE_TYPE_ARRAY:
                $fields = array_merge(
                    [
                        'performDeepMerging', 'ignoreExtraKeys', 'removeExtraKeys', 'children', 'prototype',
                        'atLeastOne', 'allowNewKeys', 'key', 'removeKeyItem', 'addDefaults', 'addDefaultChildren',
                        'nodeBuilder', 'normalizeKeys'
                    ],
                    $fields
                );
                break;
            case self::NODE_TYPE_INTEGER:
            case self::NODE_TYPE_FLOAT:
                $fields = array_merge(
                    [
                        'min', 'max'
                    ],
                    $fields
                );
                break;
            case self::NODE_TYPE_ENUM:
                $fields = array_merge(
                    [
                        'values'
                    ],
                    $fields
                );
                break;
        }
        return $fields;
    }

    /**
     * @param NodeDefinition $nodeDefinition
     * @return string
     */
    private function getNodeType(NodeDefinition $nodeDefinition): string
    {
        switch (get_class($nodeDefinition)) {
            case VariableNodeDefinition::class:
                return self::NODE_TYPE_VARIABLE;
            case ScalarNodeDefinition::class:
                return self::NODE_TYPE_SCALAR;
            case BooleanNodeDefinition::class:
                return self::NODE_TYPE_BOOLEAN;
            case ArrayNodeDefinition::class:
                return self::NODE_TYPE_ARRAY;
            case IntegerNodeDefinition::class:
                return self::NODE_TYPE_INTEGER;
            case FloatNodeDefinition::class:
                return self::NODE_TYPE_FLOAT;
            case EnumNodeDefinition::class:
                return self::NODE_TYPE_ENUM;
            default:
                throw new LogicException(sprintf('Unknown NodeDefinition "%s"', get_class($nodeDefinition)));
        }
    }
}
