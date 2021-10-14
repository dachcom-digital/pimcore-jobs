<?php

namespace JobsBundle\Controller\Admin;

use JobsBundle\Connector\ConnectorDefinitionInterface;
use JobsBundle\Connector\ConnectorEngineConfigurationInterface;
use JobsBundle\Connector\ConnectorServiceInterface;
use JobsBundle\Manager\ConnectorManagerInterface;
use JobsBundle\Manager\ContextDefinitionManagerInterface;
use JobsBundle\Service\EnvironmentServiceInterface;
use JobsBundle\Tool\FeedIdHelper;
use Pimcore\Bundle\AdminBundle\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use JobsBundle\Registry\ConnectorDefinitionRegistryInterface;
use Pimcore\Bundle\AdminBundle\Controller\AdminController;

class SettingsController extends AdminController
{
    protected EnvironmentServiceInterface $environmentService;
    protected ConnectorManagerInterface $connectorManager;
    protected ContextDefinitionManagerInterface $contextDefinitionManager;
    protected ConnectorDefinitionRegistryInterface $connectorRegistry;
    protected ConnectorServiceInterface $connectorService;

    public function __construct(
        EnvironmentServiceInterface $environmentService,
        ConnectorManagerInterface $connectorManager,
        ContextDefinitionManagerInterface $contextDefinitionManager,
        ConnectorDefinitionRegistryInterface $connectorRegistry,
        ConnectorServiceInterface $connectorService
    ) {
        $this->environmentService = $environmentService;
        $this->connectorManager = $connectorManager;
        $this->contextDefinitionManager = $contextDefinitionManager;
        $this->connectorRegistry = $connectorRegistry;
        $this->connectorService = $connectorService;
    }

    /**
     * @throws \Exception
     */
    public function getConnectorsAction(Request $request): JsonResponse
    {
        $connectors = [];
        $allConnectorDefinitions = $this->connectorManager->getAllConnectorDefinitions(true);

        foreach ($allConnectorDefinitions as $connectorDefinitionName => $connectorDefinition) {
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

    public function dataClassHealthCheckAction(Request $request): JsonResponse
    {
        $dataClassReady = false;
        $dataClass = $this->environmentService->getDataClass();
        $dataClassPath = sprintf('Pimcore\Model\DataObject\%s', ucfirst($dataClass));

        if (class_exists($dataClassPath) && method_exists($dataClassPath, 'getJobConnectorContext')) {
            $dataClassReady = true;
        }

        return $this->adminJson([
            'success'        => true,
            'dataClassPath'  => $dataClassPath,
            'dataClassReady' => $dataClassReady
        ]);
    }

    public function installConnectorAction(Request $request, string $connectorName): JsonResponse
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

    public function uninstallConnectorAction(Request $request, string $connectorName): JsonResponse
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
     * @throws \Exception
     */
    public function changeConnectorStateAction(Request $request, string $connectorName, string $stateType, string $flag = 'activate'): JsonResponse
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

    public function saveConnectorConfigurationAction(Request $request, string $connectorName): JsonResponse
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

    public function listFeedIdsAction(Request $request, string $connectorName): JsonResponse
    {
        $connectorDefinition = $this->connectorManager->getConnectorDefinition($connectorName, true);

        $feeds = [];
        if ($connectorDefinition->engineIsLoaded() === true) {
            $feedIdHelper = new FeedIdHelper($connectorDefinition->getConnectorEngine());
            $feeds = $feedIdHelper->generateFeedList($this->environmentService->getFeedHost());
        }

        return $this->adminJson([
            'success' => true,
            'feeds'   => $feeds
        ]);
    }

    public function listContextDefinitionsAction(Request $request): JsonResponse
    {
        $contextDefinitions = [];

        foreach ($this->contextDefinitionManager->getAll() as $definition) {
            $contextDefinitions[] = [
                'id'     => $definition->getId(),
                'host'   => $definition->getHost(),
                'locale' => $definition->getLocale(),
            ];
        }

        return $this->adminJson([
            'success'     => true,
            'definitions' => $contextDefinitions
        ]);
    }

    public function createContextDefinitionAction(Request $request): JsonResponse
    {
        $success = true;
        $message = null;

        $host = $request->request->get('host');
        $locale = $request->request->get('locale');

        try {
            $this->contextDefinitionManager->createNew($host, $locale);
        } catch (\Throwable $e) {
            $success = false;
            $message = $e->getMessage();
        }

        return $this->adminJson([
            'success' => $success,
            'message' => $message
        ]);
    }

    public function deleteContextDefinitionAction(Request $request): JsonResponse
    {
        $success = true;
        $message = null;

        $contextDefinitionId = $request->request->get('id');
        $contextDefinition = $this->contextDefinitionManager->getById($contextDefinitionId);

        try {
            $this->contextDefinitionManager->delete($contextDefinition);
        } catch (\Throwable $e) {
            $success = false;
            $message = $e->getMessage();
        }

        return $this->adminJson([
            'success' => $success,
            'message' => $message
        ]);
    }

    protected function getConnectorConfigurationForBackend(ConnectorDefinitionInterface $connectorDefinition): array
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
     * @throws \Exception
     */
    protected function updateConnectorConfigurationFromArray(string $connectorName, ?array $configuration): void
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
