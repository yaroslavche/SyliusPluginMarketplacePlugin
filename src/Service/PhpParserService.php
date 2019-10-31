<?php

declare(strict_types=1);

namespace Yaroslavche\SyliusPluginMarketplacePlugin\Service;

use Exception;
use PhpParser\Error;
use PhpParser\Node\Stmt;
use PhpParser\ParserFactory;
use PhpParser\PrettyPrinter\Standard;

/**
 * Class PhpParserService
 * @package Yaroslavche\SyliusPluginMarketplacePlugin\Service
 */
class PhpParserService
{
    /**
     * @param string $code
     * @return Stmt[]|null
     * @throws Exception
     */
    public function loadAst(string $code): ?array
    {
        $parser = (new ParserFactory())->create(ParserFactory::PREFER_PHP7);
        try {
            return $parser->parse($code);
        } catch (Error $error) {
            throw new Exception($error->getMessage(), $error->getCode(), $error);
        }
    }

    /**
     * @param Stmt[] $ast
     * @param string $filePath
     * @return string
     */
    public function astToCode(array $ast): string
    {
        $prettyPrinter = new Standard();
        return $prettyPrinter->prettyPrintFile($ast);
    }
}
