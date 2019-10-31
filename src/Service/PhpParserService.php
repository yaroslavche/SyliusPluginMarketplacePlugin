<?php

declare(strict_types=1);

namespace Yaroslavche\SyliusPluginMarketplacePlugin\Service;

use Exception;
use PhpParser\Error;
use PhpParser\Lexer\Emulative;
use PhpParser\Node;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\ArrayItem;
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Expr\ConstFetch;
use PhpParser\Node\Name;
use PhpParser\Node\Scalar\String_;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\Namespace_;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitorAbstract;
use PhpParser\ParserFactory;
use PhpParser\PrettyPrinter\Standard;

/**
 * Class PhpParserService
 * @package Yaroslavche\SyliusPluginMarketplacePlugin\Service
 */
class PhpParserService
{
    /** @var Emulative $lexer */
    private $lexer;

    /**
     * PhpParserService constructor.
     */
    public function __construct()
    {
        $this->lexer = new Emulative([
            'usedAttributes' => [
                'comments',
                'startLine',
                'endLine',
                'startTokenPos',
                'endTokenPos',
            ],
        ]);
    }


    /**
     * @param string $code
     * @return array<int, Node>|null
     * @throws Exception
     */
    public function codeToAst(string $code): ?array
    {
        $parser = (new ParserFactory())->create(ParserFactory::PREFER_PHP7, $this->lexer);
        try {
            return $parser->parse($code);
        } catch (Error $error) {
            throw new Exception($error->getMessage(), $error->getCode(), $error);
        }
    }

    /**
     * @param array<int, Node> $ast
     * @return string
     */
    public function astToCode(array $ast): string
    {
        return (new Standard())->prettyPrintFile($ast);
//        return (new Standard())->printFormatPreserving($ast, $oldStmts, $oldTokens);
    }

    /**
     * @param array<int, Node> $ast
     * @return string
     */
    public function getFqcnFromAst(array $ast): string
    {
        $fqcn = '';
        /** @var Node $node */
        foreach ($ast as $node) {
            if ($node instanceof Namespace_) {
                $fqcn = implode('\\', $node->name->parts) . '\\';
                foreach ($node->stmts as $stmt) {
                    if ($stmt instanceof Class_) {
                        $fqcn .= $stmt->name->toString();
                        break;
                    }
                }
            }
        }
        return $fqcn;
    }

    /**
     * @param Expr|null $key
     * @return string
     * @throws Exception
     */
    public function getFqcnFromExpr(?Expr $key): string
    {
        switch (get_class($key)) {
            case ClassConstFetch::class:
                /** @var ClassConstFetch $key */
                return implode('\\', $key->class->parts);
            default:
                throw new Exception('Unhandled bundle FQCN expression');
        }
    }

    /**
     * @param array<int, Node> $ast
     * @param string $pluginFQCN
     */
    public function insertBundle(array &$ast, string $pluginFQCN): void
    {
        $traverser = new NodeTraverser();
        $visitor = new class ($pluginFQCN) extends NodeVisitorAbstract {
            /** @var string $pluginFQCN */
            private $pluginFQCN;
            /** @var Array_ $bundlesArrayNode */
            private $bundlesArrayNode;

            /**
             *  constructor.
             * @param string $pluginFQCN
             */
            public function __construct(string $pluginFQCN)
            {
                $this->pluginFQCN = $pluginFQCN;
            }

            public function enterNode(Node $node)
            {
                if ($node instanceof Array_ && is_null($this->bundlesArrayNode)) {
                    $this->bundlesArrayNode = $node;
                }
                return parent::enterNode($node);
            }

            /** @inheritDoc */
            public function leaveNode(Node $node)
            {
                if ($node === $this->bundlesArrayNode) {
                    $classConstFetch = new ClassConstFetch(new Name($this->pluginFQCN), 'class');
                    $array = new Array_([new ArrayItem(new ConstFetch(new Name('true')), new String_('all'))]);
                    $pluginBundle = new ArrayItem($array, $classConstFetch);
                    $node->items[] = $pluginBundle;
                }
                return parent::leaveNode($node);
            }
        };
        $traverser->addVisitor($visitor);
        $ast = $traverser->traverse($ast);
    }
}
