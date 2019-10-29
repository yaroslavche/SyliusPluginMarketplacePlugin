<?php

declare(strict_types=1);

namespace Yaroslavche\SyliusMarketplacePlugin;

use Exception;
use GuzzleHttp\Client;

/**
 * Class PackagistPluginRepository
 * @package Yaroslavche\SyliusMarketplacePlugin
 */
class PackagistPluginRepository implements PluginRepositoryInterface
{
    /** @var Client $client */
    private $client;

    /**
     * PackagistPluginRepository constructor.
     */
    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => 'https://packagist.org',
            'timeout' => 2.0,
        ]);
    }

    /** @inheritDoc */
    public function find(?string $name = null): PluginCollection
    {
        $nameFilter = $name !== null ? sprintf('q=%s&', $name) : '';
        $uri = sprintf('/search.json?%stype=sylius-plugin&per_page=%d', $nameFilter, 100);
        return $this->loadPage($uri);
    }

    /**
     * @param string $uri
     * @param int $page
     * @param PluginCollection|null $collection
     * @return PluginCollection
     * @throws Exception
     */
    private function loadPage(string $uri, int $page = 1, ?PluginCollection &$collection = null): PluginCollection
    {
        $pageUri = sprintf('%s&page=%d', $uri, $page);
        $response = $this->client->get($pageUri);
        $responseObject = json_decode($response->getBody()->getContents());
        $collection = $collection ?? new PluginCollection();
        // ---
        $projectRootDir = realpath(__DIR__ . DIRECTORY_SEPARATOR . '..');
        $composerJsonFilePath = sprintf('%s%scomposer.json', $projectRootDir, DIRECTORY_SEPARATOR);
        $composerJson = json_decode(file_get_contents($composerJsonFilePath), true);
        // ---
        foreach ($responseObject->results as $package) {
            $plugin = new Plugin();
            $installed = array_key_exists($package->name, $composerJson['require']);
            $plugin
                ->setName($package->name)
                ->setInstalled($installed)
                ->setDescription($package->description)
                ->setUrl($package->url)
                ->setRepository($package->repository)
                ->setDownloads($package->downloads)
                ->setFavers($package->favers);
            $collection->add($plugin);
        }
        if (property_exists($responseObject, 'next')) {
            $this->loadPage($uri, ++$page, $collection);
        }
        return $collection;
    }
}
