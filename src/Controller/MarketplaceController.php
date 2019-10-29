<?php

declare(strict_types=1);

namespace Yaroslavche\SyliusPluginMarketplacePlugin\Controller;

use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Yaroslavche\SyliusPluginMarketplacePlugin\MarketplaceInterface;
use Yaroslavche\SyliusPluginMarketplacePlugin\Marketplace;
use Yaroslavche\SyliusPluginMarketplacePlugin\PluginInterface;

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

    /**
     * @param Request $request
     * @return PluginInterface
     * @throws Exception
     */
    private function getPlugin(Request $request): PluginInterface
    {
        $pluginName = $request->request->get('name');
        if (null === $pluginName) {
            throw new Exception('Invalid plugin name');
        }
        return $this->marketplace->getPluginRepository()->find($pluginName)->get($pluginName);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function install(Request $request): JsonResponse
    {
        try {
            $plugin = $this->getPlugin($request);
            $this->marketplace->installPlugin($plugin);
            return $this->json(['status' => 'success', 'message' => sprintf('%s installed', $plugin->getName())]);
        } catch (Exception $exception) {
            return $this->json(['status' => 'error', 'message' => $exception->getMessage()]);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function uninstall(Request $request): JsonResponse
    {
        try {
            $plugin = $this->getPlugin($request);
            $this->marketplace->uninstallPlugin($plugin);
            return $this->json(['status' => 'success', 'message' => sprintf('%s uninstalled', $plugin->getName())]);
        } catch (Exception $exception) {
            return $this->json(['status' => 'error', 'message' => $exception->getMessage()]);
        }
    }
}
