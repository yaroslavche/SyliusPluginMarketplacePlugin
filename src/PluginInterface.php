<?php

declare(strict_types=1);

namespace Yaroslavche\SyliusPluginMarketplacePlugin;

/**
 * Interface PluginInterface
 * @package Yaroslavche\SyliusPluginMarketplacePlugin
 */
interface PluginInterface
{
    /**
     * @return string
     */
    public function getName(): string;

    /**
     * @param string $name
     * @return PluginInterface
     */
    public function setName(string $name): PluginInterface;

    /**
     * @return string
     */
    public function getDescription(): string;

    /**
     * @param string $description
     * @return PluginInterface
     */
    public function setDescription(string $description): PluginInterface;

    /**
     * @return string
     */
    public function getUrl(): string;

    /**
     * @param string $url
     * @return PluginInterface
     */
    public function setUrl(string $url): PluginInterface;

    /**
     * @return string
     */
    public function getRepository(): string;

    /**
     * @param string $repository
     * @return PluginInterface
     */
    public function setRepository(string $repository): PluginInterface;

    /**
     * @return int
     */
    public function getDownloads(): int;

    /**
     * @param int $downloads
     * @return PluginInterface
     */
    public function setDownloads(int $downloads): PluginInterface;

    /**
     * @return int
     */
    public function getFavers(): int;

    /**
     * @param int $favers
     * @return PluginInterface
     */
    public function setFavers(int $favers): PluginInterface;

    /**
     * @return bool
     */
    public function isInstalled(): bool;

    /**
     * @param bool $installed
     * @return PluginInterface
     */
    public function setInstalled(bool $installed): PluginInterface;
}
