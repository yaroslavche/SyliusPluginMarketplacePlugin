<?php

declare(strict_types=1);

namespace Yaroslavche\SyliusPluginMarketplacePlugin\Service;

use Exception;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Class FilesystemService
 * @package Yaroslavche\SyliusPluginMarketplacePlugin\Service
 */
class FilesystemService
{
    /** @var Filesystem $filesystem */
    private $filesystem;

    /**
     * FilesystemService constructor.
     */
    public function __construct()
    {
        $this->filesystem = new Filesystem();
    }


    /**
     * @param string $path
     * @return bool
     */
    public function fileExists(string $path): bool
    {
        return $this->filesystem->exists($path);
    }

    /**
     * @param string $path
     * @return string
     * @throws Exception
     */
    public function loadFileContent(string $path): string
    {
        if (!$this->filesystem->exists($path)) {
            throw new Exception('Invalid path');
        }
        return file_get_contents($path);
    }

    /**
     * @param string $path
     * @param string $content
     */
    public function saveFileContent(string $path, string $content): void
    {
        if ($this->filesystem->exists($path)) {
            $this->filesystem->rename($path, sprintf('%s.%s.bak', $path, time()));
        }
        $this->filesystem->appendToFile($path, $content);
    }

    /**
     * @param string $path
     */
    public function remove(string $path): void
    {
        $this->filesystem->remove($path);
    }
}
