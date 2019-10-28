<?php

declare(strict_types=1);

namespace Yaroslavche\SyliusMarketplacePlugin\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
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
        return $this->render('@YaroslavcheSyliusMarketplacePlugin/index.html.twig');
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function list(Request $request): JsonResponse
    {
        return $this->json($this->marketplace->getPluginRepository()->find());
    }
}
