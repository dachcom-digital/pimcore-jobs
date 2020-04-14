<?php

namespace JobsBundle\Connector;

use JobsBundle\Feed\FeedGeneratorInterface;
use JobsBundle\Manager\ConnectorManagerInterface;
use JobsBundle\Model\ConnectorEngineInterface;
use JobsBundle\Registry\ConnectorDefinitionRegistryInterface;

class ConnectorService implements ConnectorServiceInterface
{
    /**
     * @var array|ConnectorEngineInterface[]
     */
    protected $connectorCache = [];

    /**
     * @var ConnectorDefinitionRegistryInterface
     */
    protected $connectorDefinitionRegistry;

    /**
     * @var ConnectorManagerInterface
     */
    protected $connectorManager;

    /**
     * @param ConnectorDefinitionRegistryInterface $connectorDefinitionRegistry
     * @param ConnectorManagerInterface            $connectorManager
     */
    public function __construct(
        ConnectorDefinitionRegistryInterface $connectorDefinitionRegistry,
        ConnectorManagerInterface $connectorManager
    ) {
        $this->connectorCache = [];
        $this->connectorDefinitionRegistry = $connectorDefinitionRegistry;
        $this->connectorManager = $connectorManager;
    }

    /**
     * {@inheritDoc}
     */
    public function installConnector(string $connectorName)
    {
        $connectorDefinition = $this->getConnectorDefinition($connectorName, true);
        if ($connectorDefinition->engineIsLoaded()) {
            throw new \Exception(sprintf('Cannot install "%s". Connector already exists.', $connectorName));
        }

        $connector = $this->connectorManager->createNewEngine($connectorName);

        return $connector;
    }

    /**
     * {@inheritDoc}
     */
    public function uninstallConnector(string $connectorName)
    {
        $connectorDefinition = $this->getConnectorDefinition($connectorName, true);
        if (!$connectorDefinition->engineIsLoaded()) {
            throw new \Exception(sprintf('Cannot uninstall "%s". Connector does not exist.', $connectorName));
        }

        if ($connectorDefinition->getConnectorEngine()->isEnabled() === true) {
            throw new \Exception(sprintf('Cannot uninstall "%s". Connector is currently enabled.', $connectorName));
        }

        $this->connectorManager->deleteEngineByName($connectorName);
    }

    /**
     * {@inheritDoc}
     */
    public function enableConnector(string $connectorName)
    {
        $connectorDefinition = $this->getConnectorDefinition($connectorName, true);

        if (!$connectorDefinition->engineIsLoaded()) {
            throw new \Exception(sprintf('Cannot enable "%s". Connector does not exist.', $connectorName));
        }

        $connectorEngine = $connectorDefinition->getConnectorEngine();
        if ($connectorEngine->isEnabled() === true) {
            throw new \Exception(sprintf('Cannot enable "%s". Connector already enabled.', $connectorName));
        }

        try {
            $connectorDefinition->beforeEnable();
        } catch (\Exception $e) {
            throw new \Exception(sprintf('Cannot enable "%s". %s.', $connectorName, $e->getMessage()));
        }

        $connectorEngine->setEnabled(true);

        $this->connectorManager->updateEngine($connectorEngine);
    }

    /**
     * {@inheritDoc}
     */
    public function disableConnector(string $connectorName)
    {
        $connectorDefinition = $this->getConnectorDefinition($connectorName, true);

        if (!$connectorDefinition->engineIsLoaded()) {
            throw new \Exception(sprintf('Cannot disable "%s". Connector does not exist.', $connectorName));
        }

        $connectorEngine = $connectorDefinition->getConnectorEngine();
        if ($connectorEngine->isEnabled() === false) {
            throw new \Exception(sprintf('Cannot disable "%s". Connector already disabled.', $connectorName));
        }

        try {
            $connectorDefinition->beforeDisable();
        } catch (\Exception $e) {
            throw new \Exception(sprintf('Cannot disable "%s". %s.', $connectorName, $e->getMessage()));
        }

        $connectorEngine->setEnabled(false);

        $this->connectorManager->updateEngine($connectorEngine);
    }

    /**
     * {@inheritDoc}
     */
    public function connectConnector(string $connectorName)
    {
        $connectorDefinition = $this->getConnectorDefinition($connectorName, true);

        if (!$connectorDefinition->engineIsLoaded()) {
            throw new \Exception(sprintf('Cannot connect "%s". Connector does not exist.', $connectorName));
        }

        if (!$connectorDefinition->getConnectorEngine()->isEnabled()) {
            throw new \Exception(sprintf('Cannot connect  "%s". Connector is not enabled.', $connectorName));
        }

        try {
            $connectorDefinition->connect();
        } catch (\Exception $e) {
            throw new \Exception(sprintf('Cannot connect "%s". %s.', $connectorName, $e->getMessage()));
        }
    }

    /**
     * {@inheritDoc}
     */
    public function disconnectConnector(string $connectorName)
    {
        $connectorDefinition = $this->getConnectorDefinition($connectorName, true);

        if (!$connectorDefinition->engineIsLoaded()) {
            throw new \Exception(sprintf('Cannot disconnect "%s". Connector does not exist.', $connectorName));
        }

        if (!$connectorDefinition->getConnectorEngine()->isEnabled()) {
            throw new \Exception(sprintf('Cannot disconnect  "%s". Connector is not enabled.', $connectorName));
        }

        try {
            $connectorDefinition->disconnect();
        } catch (\Exception $e) {
            throw new \Exception(sprintf('Cannot disconnect "%s". %s.', $connectorName, $e->getMessage()));
        }
    }

    /**
     * {@inheritDoc}
     */
    public function generateConnectorFeed(string $connectorName, string $outputType, array $items, array $params = [])
    {
        $connectorDefinition = $this->getConnectorDefinition($connectorName, true);
        $feedGenerator = $connectorDefinition->buildFeedGenerator($items, $params);

        if (!$feedGenerator instanceof FeedGeneratorInterface) {
            throw new \Exception(sprintf('Feed generation for "%s" failed.', $connectorName));
        }

        return $feedGenerator->generate($outputType);
    }

    /**
     * {@inheritDoc}
     */
    public function updateConnectorFeedIds(string $connectorName, array $feedIds)
    {
        $connectorDefinition = $this->getConnectorDefinition($connectorName, true);

        if (!$connectorDefinition->engineIsLoaded()) {
            throw new \Exception(sprintf('Cannot fetch configuration for "%s". Connector Engine is not loaded.', $connectorName));
        }

        $connectorEngine = $connectorDefinition->getConnectorEngine();
        $connectorEngine->setFeedIds($feedIds);
        $this->connectorManager->updateEngine($connectorEngine);
    }

    /**
     * {@inheritDoc}
     */
    public function updateConnectorEngineConfiguration(string $connectorName, ConnectorEngineConfigurationInterface $connectorConfiguration)
    {
        $connectorDefinition = $this->getConnectorDefinition($connectorName, true);

        if (!$connectorDefinition->engineIsLoaded()) {
            throw new \Exception(sprintf('Cannot fetch configuration for "%s". Connector Engine is not loaded.', $connectorName));
        }

        $connectorEngine = $connectorDefinition->getConnectorEngine();
        $connectorEngine->setConfiguration(clone $connectorConfiguration);
        $this->connectorManager->updateEngine($connectorEngine);
    }

    /**
     * {@inheritDoc}
     */
    public function connectorDefinitionIsEnabled(string $connectorDefinition)
    {
        return $this->connectorManager->connectorDefinitionIsEnabled($connectorDefinition);
    }

    /**
     * {@inheritDoc}
     */
    public function getConnectorDefinition(string $connectorName, bool $loadEngine = false)
    {
        return $this->connectorManager->getConnectorDefinition($connectorName, $loadEngine);
    }

}
