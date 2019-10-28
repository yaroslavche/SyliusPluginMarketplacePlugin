<?php

declare(strict_types=1);

namespace Yaroslavche\SyliusMarketplacePlugin\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Yaroslavche\SyliusMarketplacePlugin\MarketplaceInterface;
use Yaroslavche\SyliusMarketplacePlugin\Marketplace;

/**
 * Class MarketplaceController
 * @package Yaroslavche\SyliusMarketplacePlugin\Controller
 */
final class MarketplaceController extends AbstractController
{
    /**
     * @var MarketplaceInterface $marketplace
     */
    private $marketplace;

    /**
     * MarketplaceController constructor.
     */
    public function __construct()
    {
        $this->marketplace = new Marketplace();
    }

    /**
     * @return Response
     */
    public function index(): Response
    {
        $plugins = $this->marketplace->getPluginRepository()->find();
        return $this->render('@YaroslavcheSyliusMarketplacePlugin/base.html.twig', [
            'plugins' => $plugins
        ]);
    }
}
