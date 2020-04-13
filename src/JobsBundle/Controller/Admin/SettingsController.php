<?php

namespace JobsBundle\Controller\Admin;

use JobsBundle\Connector\ConnectorDefinitionInterface;
use JobsBundle\Connector\ConnectorEngineConfigurationInterface;
use JobsBundle\Connector\ConnectorServiceInterface;
use JobsBundle\Manager\ConnectorManagerInterface;
use Pimcore\Bundle\AdminBundle\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use JobsBundle\Registry\ConnectorDefinitionRegistryInterface;
use Pimcore\Bundle\AdminBundle\Controller\AdminController;

class SettingsController extends AdminController
{
    /**
     * @var ConnectorManagerInterface
     */
    protected $connectorManager;

    /**
     * @var ConnectorDefinitionRegistryInterface
     */
    protected $connectorRegistry;

    /**
     * @var ConnectorServiceInterface
     */
    protected $connectorService;

    /**
     * @param ConnectorManagerInterface            $connectorManager
     * @param ConnectorDefinitionRegistryInterface $connectorRegistry
     * @param ConnectorServiceInterface            $connectorService
     */
    public function __construct(
        ConnectorManagerInterface $connectorManager,
        ConnectorDefinitionRegistryInterface $connectorRegistry,
        ConnectorServiceInterface $connectorService
    ) {
        $this->connectorManager = $connectorManager;
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

        foreach ($this->connectorManager->getAllConnectorDefinitions(true) as $connectorDefinitionName => $connectorDefinition) {

            $engineConfiguration = null;
            $isInstalled = $connectorDefinition->engineIsLoaded();

            if ($isInstalled && $connectorDefinition->needsEngineConfiguration()) {
                $engineConfiguration = $this->getConnectorConfigurationForBackend($connectorDefinition);
            }

            $config = [
                'installed'           => $isInstalled,
                'enabled'             => $isInstalled && $connectorDefinition->getConnectorEngine()->isEnabled(),
                'connected'           => $isInstalled && $connectorDefinition->isConnected(),
                'token'               => $isInstalled ? $connectorDefinition->getConnectorEngine()->getToken() : null,
                'autoConnect'         => $connectorDefinition->isAutoConnected(),
                'customConfiguration' => $engineConfiguration
            ];

            $connectors[] = [
                'name'   => $connectorDefinitionName,
                'label'  => ucfirst($connectorDefinitionName),
                'config' => $config
            ];
        }

        return $this->adminJson([
            'success'    => true,
            'connectors' => $connectors
        ]);
    }

    /**
     * @param Request $request
     * @param string  $connectorName
     *
     * @return JsonResponse
     */
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

    /**
     * @param Request $request
     * @param string  $connectorName
     *
     * @return JsonResponse
     */
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

    /**
     * @param Request $request
     * @param string  $connectorName
     * @param string  $stateType
     * @param string  $flag
     *
     * @return JsonResponse
     * @throws \Exception
     */
    public function changeConnectorStateAction(Request $request, string $connectorName, string $stateType, string $flag = 'activate')
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

    /**
     * @param Request $request
     * @param string  $connectorName
     *
     * @return JsonResponse
     */
    public function saveConnectorConfigurationAction(Request $request, string $connectorName)
    {
        $success = true;
        $message = null;

        $configuration = json_decode($request->request->get('configuration'), true);

        try {
            $this->updateConnectorConfigurationFromArray($connectorName, $configuration);
        } catch (\Throwable $e) {
            $success = false;
            $message = $e->getMessage();
        }

        return $this->adminJson([
            'success' => $success,
            'message' => $message
        ]);
    }

    /**
     * @param ConnectorDefinitionInterface $connectorDefinition
     *
     * @return array
     */
    protected function getConnectorConfigurationForBackend(ConnectorDefinitionInterface $connectorDefinition)
    {
        if (!$connectorDefinition->engineIsLoaded()) {
            return [];
        }

        $engineConfiguration = $connectorDefinition->getConnectorEngine()->getConfiguration();
        if (!$engineConfiguration instanceof ConnectorEngineConfigurationInterface) {
            return [];
        }

        return $engineConfiguration->toBackendConfigArray();
    }

    /**
     * @param string     $connectorName
     * @param array|null $configuration
     *
     * @throws \Exception
     */
    protected function updateConnectorConfigurationFromArray(string $connectorName, ?array $configuration)
    {
        $connectorDefinition = $this->connectorManager->getConnectorDefinition($connectorName, true);

        try {
            $connectorConfiguration = $connectorDefinition->mapEngineConfigurationFromBackend($configuration);
        } catch (\Throwable $e) {
            throw new \Exception(sprintf('Error while processing backend configuration for %s": %s', $connectorName, $e->getMessage()), 0, $e);
        }

        if (!$connectorConfiguration instanceof ConnectorEngineConfigurationInterface) {
            return;
        }

        $connectorEngine = $connectorDefinition->getConnectorEngine();
        $connectorEngine->setConfiguration($connectorConfiguration);

        $this->connectorService->updateConnectorEngineConfiguration($connectorName, $connectorConfiguration);
    }

}
