<?php

namespace JobsBundle\Controller\Admin;

use JobsBundle\Connector\ConnectorServiceInterface;
use Pimcore\Bundle\AdminBundle\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use JobsBundle\Registry\ConnectorRegistryInterface;
use Pimcore\Bundle\AdminBundle\Controller\AdminController;

class SettingsController extends AdminController
{
    protected $enabledConnectors;

    protected $connectorRegistry;

    protected $connectorService;

    /**
     * @param array                      $enabledConnectors
     * @param ConnectorRegistryInterface $connectorRegistry
     * @param ConnectorServiceInterface  $connectorService
     */
    public function __construct(
        array $enabledConnectors,
        ConnectorRegistryInterface $connectorRegistry,
        ConnectorServiceInterface $connectorService
    ) {
        $this->enabledConnectors = $enabledConnectors;
        $this->connectorRegistry = $connectorRegistry;
        $this->connectorService = $connectorService;
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     * @throws \Exception
     */
    public function getConnectorsAction(Request $request)
    {
        $connectors = [];

        foreach ($this->enabledConnectors as $connectorName) {

            if (!$this->connectorRegistry->has($connectorName)) {
                continue;
            }

            $customConfiguration = null;
            $connector = $this->connectorRegistry->get($connectorName);
            $isInstalled = $this->connectorService->connectorIsInstalled($connectorName);

            if ($isInstalled && $this->connectorService->connectorHasCustomConfig($connectorName)) {
                $customConfiguration = $this->connectorService->getConnectorConfigurationForBackend($connectorName);
            }

            $config = [
                'installed'           => $isInstalled,
                'enabled'             => $isInstalled && $this->connectorService->connectorIsEnabled($connectorName),
                'connected'           => $isInstalled && $this->connectorService->connectorIsConnected($connectorName),
                'token'               => $isInstalled ? $this->connectorService->getConnectorToken($connectorName) : null,
                'autoConnect'         => $connector->isAutoConnected(),
                'customConfiguration' => $customConfiguration
            ];

            $connectors[] = [
                'name'   => $connectorName,
                'label'  => ucfirst($connectorName),
                'config' => $config
            ];
        }

        return $this->adminJson([
            'success'    => true,
            'connectors' => $connectors
        ]);
    }

    public function installConnectorAction(Request $request, string $connectorName)
    {
        $token = null;
        $success = true;
        $message = null;
        $installed = false;

        try {
            $connector = $this->connectorService->installConnector($connectorName);
            $token = $connector->getToken();
            $installed = true;
        } catch (\Throwable $e) {
            $success = false;
            $message = $e->getMessage();
        }

        return $this->adminJson([
            'success'   => $success,
            'message'   => $message,
            'token'     => $token,
            'installed' => $installed
        ]);
    }

    public function uninstallConnectorAction(Request $request, string $connectorName)
    {
        $success = true;
        $message = null;
        $installed = true;

        try {
            $this->connectorService->uninstallConnector($connectorName);
            $installed = false;
        } catch (\Throwable $e) {
            $success = false;
            $message = $e->getMessage();
        }

        return $this->adminJson([
            'success'   => $success,
            'message'   => $message,
            'installed' => $installed
        ]);
    }

    public function changeConnectorState(Request $request, string $connectorName, string $stateType, string $flag = 'activate')
    {
        $success = true;
        $message = null;
        $stateMode = null;

        switch ($stateType) {
            case 'availability':
                try {
                    if ($flag === 'activate') {
                        $stateMode = 'activated';
                        $this->connectorService->enableConnector($connectorName);
                    } else {
                        $stateMode = 'deactivated';
                        $this->connectorService->disableConnector($connectorName);
                    }
                } catch (\Exception $e) {
                    $success = false;
                    $message = $e->getMessage();
                }
                break;
            case 'connection':
                try {
                    if ($flag === 'activate') {
                        $stateMode = 'activated';
                        $this->connectorService->connectConnector($connectorName);
                    } else {
                        $stateMode = 'deactivated';
                        $this->connectorService->disconnectConnector($connectorName);
                    }
                } catch (\Exception $e) {
                    $success = false;
                    $message = $e->getMessage();
                }
                break;
            default:
                throw new \Exception(sprintf('Invalid state type "%s"', $stateType));

                break;
        }

        return $this->adminJson([
            'success'   => $success,
            'message'   => $message,
            'stateMode' => $stateMode
        ]);
    }

    public function saveConnectorConfiguration(Request $request, string $connectorName)
    {
        $success = true;
        $message = null;

        $configuration = json_decode($request->request->get('configuration'), true);

        try {
            $this->connectorService->updateConnectorConfigurationFromArray($connectorName, $configuration);
        } catch (\Throwable $e) {
            $success = false;
            $message = $e->getMessage();
        }

        return $this->adminJson([
            'success' => $success,
            'message' => $message
        ]);
    }
}
