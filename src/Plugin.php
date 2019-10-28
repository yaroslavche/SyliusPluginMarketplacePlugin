<?php

declare(strict_types=1);

namespace Yaroslavche\SyliusMarketplacePlugin;

/**
 * Class Plugin
 * @package Yaroslavche\SyliusMarketplacePlugin
 */
class Plugin implements PluginInterface
{
    /** @var string $name */
    private $name;
    /** @var string $description */
    private $description;
    /** @var string $url */
    private $url;
    /** @var string $repository */
    private $repository;
    /** @var int $downloads */
    private $downloads;
    /** @var int $favers */
    private $favers;

    /** @inheritDoc */
    public function install(): void
    {
    }

    /** @inheritDoc */
    public function uninstall(): void
    {
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return PluginInterface
     */
    public function setName(string $name): PluginInterface
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @param string $description
     * @return PluginInterface
     */
    public function setDescription(string $description): PluginInterface
    {
        $this->description = $description;
        return $this;
    }

    /**
     * @return string
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * @param string $url
     * @return PluginInterface
     */
    public function setUrl(string $url): PluginInterface
    {
        $this->url = $url;
        return $this;
    }

    /**
     * @return string
     */
    public function getRepository(): string
    {
        return $this->repository;
    }

    /**
     * @param string $repository
     * @return PluginInterface
     */
    public function setRepository(string $repository): PluginInterface
    {
        $this->repository = $repository;
        return $this;
    }

    /**
     * @return int
     */
    public function getDownloads(): int
    {
        return $this->downloads;
    }

    /**
     * @param int $downloads
     * @return PluginInterface
     */
    public function setDownloads(int $downloads): PluginInterface
    {
        $this->downloads = $downloads;
        return $this;
    }

    /**
     * @return int
     */
    public function getFavers(): int
    {
        return $this->favers;
    }

    /**
     * @param int $favers
     * @return PluginInterface
     */
    public function setFavers(int $favers): PluginInterface
    {
        $this->favers = $favers;
        return $this;
    }
}
