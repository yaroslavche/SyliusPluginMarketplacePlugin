<?php

declare(strict_types=1);

namespace Yaroslavche\SyliusMarketplacePlugin\Controller;

use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
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
     * @return JsonResponse
     */
    public function list(): JsonResponse
    {
        try {
            $plugins = $this->marketplace->getPluginRepository()->find();
        } catch (Exception $exception) {
            return $this->json(['status' => 'error', 'message' => $exception->getMessage()]);
        }
        return $this->json(['status' => 'success', 'plugins' => $plugins]);
    }
}
