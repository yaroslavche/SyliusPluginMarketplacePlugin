<?php

declare(strict_types=1);

namespace Yaroslavche\SyliusPluginMarketplacePlugin;

/**
 * Class Plugin
 * @package Yaroslavche\SyliusPluginMarketplacePlugin
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
    /** @var bool $installed */
    private $installed;

    /** @inheritDoc */
    public function install(): void
    {
    }

    /** @inheritDoc */
    public function uninstall(): void
    {
    }

    /** @inheritDoc */
    public function getName(): string
    {
        return $this->name;
    }

    /** @inheritDoc */
    public function setName(string $name): PluginInterface
    {
        $this->name = $name;
        return $this;
    }

    /** @inheritDoc */
    public function getDescription(): string
    {
        return $this->description;
    }

    /** @inheritDoc */
    public function setDescription(string $description): PluginInterface
    {
        $this->description = $description;
        return $this;
    }

    /** @inheritDoc */
    public function getUrl(): string
    {
        return $this->url;
    }

    /** @inheritDoc */
    public function setUrl(string $url): PluginInterface
    {
        $this->url = $url;
        return $this;
    }

    /** @inheritDoc */
    public function getRepository(): string
    {
        return $this->repository;
    }

    /** @inheritDoc */
    public function setRepository(string $repository): PluginInterface
    {
        $this->repository = $repository;
        return $this;
    }

    /** @inheritDoc */
    public function getDownloads(): int
    {
        return $this->downloads;
    }

    /** @inheritDoc */
    public function setDownloads(int $downloads): PluginInterface
    {
        $this->downloads = $downloads;
        return $this;
    }

    /** @inheritDoc */
    public function getFavers(): int
    {
        return $this->favers;
    }

    /** @inheritDoc */
    public function setFavers(int $favers): PluginInterface
    {
        $this->favers = $favers;
        return $this;
    }

    /** @inheritDoc */
    public function isInstalled(): bool
    {
        return $this->installed;
    }

    /** @inheritDoc */
    public function setInstalled(bool $installed): PluginInterface
    {
        $this->installed = $installed;
        return $this;
    }
}
