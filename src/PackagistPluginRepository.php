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
        $response = $this->client->get($uri);
        $responseObject = json_decode($response->getBody()->getContents());
        $collection = new PluginCollection();
        foreach ($responseObject->results as $package) {
            $plugin = new Plugin();
            $plugin
                ->setName($package->name)
                ->setDescription($package->description)
                ->setUrl($package->url)
                ->setRepository($package->repository)
                ->setDownloads($package->downloads)
                ->setFavers($package->favers);
            $collection->add($plugin);
        }
        return $collection;
    }
}
