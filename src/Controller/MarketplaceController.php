<?php

declare(strict_types=1);

namespace Yaroslavche\SyliusPluginMarketplacePlugin\Controller;

use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Yaroslavche\SyliusPluginMarketplacePlugin\MarketplaceInterface;
use Yaroslavche\SyliusPluginMarketplacePlugin\Marketplace;

/**
 * Class MarketplaceController
 * @package Yaroslavche\SyliusPluginMarketplacePlugin\Controller
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
        return $this->render('YaroslavcheSyliusPluginMarketplacePlugin::index.html.twig');
    }

    /**
     * @return JsonResponse
     */
    public function list(): JsonResponse
    {
        try {
            $plugins = $this->marketplace->list();
        } catch (Exception $exception) {
            return $this->json(['status' => 'error', 'message' => $exception->getMessage()]);
        }
        return $this->json(['status' => 'success', 'plugins' => $plugins]);
    }
}
