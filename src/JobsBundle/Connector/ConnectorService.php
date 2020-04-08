<?php

namespace JobsBundle\Connector;

use JobsBundle\Manager\ConnectorDefinitionManagerInterface;
use JobsBundle\Model\ConnectorDefinitionInterface;
use JobsBundle\Registry\ConnectorRegistryInterface;

class ConnectorService implements ConnectorServiceInterface
{
    protected $connectorRegistry;

    protected $connectorDefinitionManager;

    /**
     * @param ConnectorRegistryInterface          $connectorRegistry
     * @param ConnectorDefinitionManagerInterface $connectorDefinitionManager
     */
    public function __construct(
        ConnectorRegistryInterface $connectorRegistry,
        ConnectorDefinitionManagerInterface $connectorDefinitionManager
    ) {
        $this->connectorRegistry = $connectorRegistry;
        $this->connectorDefinitionManager = $connectorDefinitionManager;
    }

    /**
     * {@inheritDoc}
     */
    public function connectorIsInstalled(string $connectorName)
    {
        $connectorDefinition = $this->connectorDefinitionManager->getByName($connectorName);

        return $connectorDefinition instanceof ConnectorDefinitionInterface;
    }

    /**
     * {@inheritDoc}
     */
    public function connectorIsEnabled(string $connectorName)
    {
        $connectorDefinition = $this->connectorDefinitionManager->getByName($connectorName);
        if (!$connectorDefinition instanceof ConnectorDefinitionInterface) {
            return false;
        }

        return $connectorDefinition->isEnabled();
    }

    /**
     * {@inheritDoc}
     */
    public function connectorIsConnected(string $connectorName)
    {
        $connector = $this->connectorRegistry->get($connectorName);

        $connectorDefinition = $this->connectorDefinitionManager->getByName($connectorName);
        if (!$connectorDefinition instanceof ConnectorDefinitionInterface) {
            return false;
        }

        return $connector->isConnected($connectorDefinition->getConfiguration());
    }

    /**
     * {@inheritDoc}
     */
    public function connectorHasDataFeed(string $connectorName)
    {
        $connector = $this->connectorRegistry->get($connectorName);

        return $connector->hasDataFeed();
    }

    /**
     * {@inheritDoc}
     */
    public function installConnector(string $connectorName)
    {
        $connectorDefinition = $this->connectorDefinitionManager->getByName($connectorName);
        if ($connectorDefinition instanceof ConnectorDefinitionInterface) {
            throw new \Exception(sprintf('Cannot install "%s". Connector already exists.', $connectorName));
        }

        $connector = $this->connectorDefinitionManager->createNew($connectorName);

        return $connector;
    }

    /**
     * {@inheritDoc}
     */
    public function uninstallConnector(string $connectorName)
    {
        $connectorDefinition = $this->connectorDefinitionManager->getByName($connectorName);
        if (!$connectorDefinition instanceof ConnectorDefinitionInterface) {
            throw new \Exception(sprintf('Cannot uninstall "%s". Connector does not exist.', $connectorName));
        }

        if ($connectorDefinition->isEnabled() === true) {
            throw new \Exception(sprintf('Cannot uninstall "%s". Connector is currently enabled.', $connectorName));
        }

        $this->connectorDefinitionManager->deleteByName($connectorName);
    }

    /**
     * {@inheritDoc}
     */
    public function enableConnector(string $connectorName)
    {
        $connector = $this->connectorRegistry->get($connectorName);
        $connectorDefinition = $this->connectorDefinitionManager->getByName($connectorName);

        if (!$connectorDefinition instanceof ConnectorDefinitionInterface) {
            throw new \Exception(sprintf('Cannot enable "%s". Connector does not exist.', $connectorName));
        }

        if ($connectorDefinition->isEnabled() === true) {
            throw new \Exception(sprintf('Cannot enable "%s". Connector already enabled.', $connectorName));
        }

        try {
            $connector->beforeEnable();
        } catch (\Exception $e) {
            throw new \Exception(sprintf('Cannot enable "%s". %s.', $connectorName, $e->getMessage()));
        }

        $connectorDefinition->setEnabled(true);

        $this->connectorDefinitionManager->update($connectorDefinition);
    }

    /**
     * {@inheritDoc}
     */
    public function disableConnector(string $connectorName)
    {
        $connector = $this->connectorRegistry->get($connectorName);
        $connectorDefinition = $this->connectorDefinitionManager->getByName($connectorName);
        if (!$connectorDefinition instanceof ConnectorDefinitionInterface) {
            throw new \Exception(sprintf('Cannot disable "%s". Connector does not exist.', $connectorName));
        }

        if ($connectorDefinition->isEnabled() === false) {
            throw new \Exception(sprintf('Cannot disable "%s". Connector already disabled.', $connectorName));
        }

        try {
            $connector->beforeDisable();
        } catch (\Exception $e) {
            throw new \Exception(sprintf('Cannot disable "%s". %s.', $connectorName, $e->getMessage()));
        }

        $connectorDefinition->setEnabled(false);

        $this->connectorDefinitionManager->update($connectorDefinition);
    }

    /**
     * {@inheritDoc}
     */
    public function connectConnector(string $connectorName)
    {
        $connector = $this->connectorRegistry->get($connectorName);
        $connectorDefinition = $this->connectorDefinitionManager->getByName($connectorName);
        if (!$connectorDefinition instanceof ConnectorDefinitionInterface) {
            throw new \Exception(sprintf('Cannot connect "%s". Connector does not exist.', $connectorName));
        }

        if (!$connectorDefinition->isEnabled()) {
            throw new \Exception(sprintf('Cannot connect  "%s". Connector is not enabled.', $connectorName));
        }

        try {
            $connector->connect($connectorDefinition->getConfiguration());
        } catch (\Exception $e) {
            throw new \Exception(sprintf('Cannot connect "%s". %s.', $connectorName, $e->getMessage()));
        }
    }

    /**
     * {@inheritDoc}
     */
    public function disconnectConnector(string $connectorName)
    {
        $connector = $this->connectorRegistry->get($connectorName);
        $connectorDefinition = $this->connectorDefinitionManager->getByName($connectorName);
        if (!$connectorDefinition instanceof ConnectorDefinitionInterface) {
            throw new \Exception(sprintf('Cannot disconnect "%s". Connector does not exist.', $connectorName));
        }

        if (!$connectorDefinition->isEnabled()) {
            throw new \Exception(sprintf('Cannot disconnect  "%s". Connector is not enabled.', $connectorName));
        }

        try {
            $connector->disconnect($connectorDefinition->getConfiguration());
        } catch (\Exception $e) {
            throw new \Exception(sprintf('Cannot disconnect "%s". %s.', $connectorName, $e->getMessage()));
        }
    }

    /**
     * {@inheritDoc}
     */
    public function getConnectorToken(string $connectorName)
    {
        $connectorDefinition = $this->connectorDefinitionManager->getByName($connectorName);
        if (!$connectorDefinition instanceof ConnectorDefinitionInterface) {
            throw new \Exception(sprintf('Cannot fetch token for "%s". Connector does not exist.', $connectorName));
        }

        return $connectorDefinition->getToken();
    }

    /**
     * {@inheritDoc}
     */
    public function connectorHasCustomConfig(string $connectorName)
    {
        $connector = $this->connectorRegistry->get($connectorName);

        return $connector->getConfigurationClass() !== null;
    }

    /**
     * {@inheritDoc}
     */
    public function getConnectorConfiguration(string $connectorName)
    {
        $connectorDefinition = $this->connectorDefinitionManager->getByName($connectorName);

        if (!$connectorDefinition instanceof ConnectorDefinitionInterface) {
            return null;
        }

        $configuration = $connectorDefinition->getConfiguration();
        if (!$configuration instanceof JobsConnectorConfigurationInterface) {
            return null;
        }

        return $configuration;
    }

    /**
     * {@inheritDoc}
     */
    public function getConnectorConfigurationForBackend(string $connectorName)
    {
        $connectorDefinition = $this->connectorDefinitionManager->getByName($connectorName);
        if (!$connectorDefinition instanceof ConnectorDefinitionInterface) {
            return [];
        }

        $configuration = $connectorDefinition->getConfiguration();
        if (!$configuration instanceof JobsConnectorConfigurationInterface) {
            return [];
        }

        return $configuration->toBackendConfigArray();
    }

    /**
     * {@inheritDoc}
     */
    public function updateConnectorConfiguration(string $connectorName, JobsConnectorConfigurationInterface $connectorConfiguration)
    {
        $connectorDefinition = $this->connectorDefinitionManager->getByName($connectorName);

        if (!$connectorDefinition instanceof ConnectorDefinitionInterface) {
            throw new \Exception(sprintf('Cannot fetch configuration for "%s". Connector does not exist.', $connectorName));
        }

        $refreshedConnectorConfiguration = clone $connectorConfiguration;

        $connectorDefinition->setConfiguration($refreshedConnectorConfiguration);

        $this->connectorDefinitionManager->update($connectorDefinition);
    }

    /**
     * {@inheritDoc}
     */
    public function updateConnectorConfigurationFromArray(string $connectorName, ?array $configuration)
    {
        $connector = $this->connectorRegistry->get($connectorName);
        $connectorDefinition = $this->connectorDefinitionManager->getByName($connectorName);

        if (!$connectorDefinition instanceof ConnectorDefinitionInterface) {
            throw new \Exception(sprintf('Cannot fetch configuration for "%s". Connector does not exist.', $connectorName));
        }

        if ($connectorDefinition->getConfiguration() instanceof JobsConnectorConfigurationInterface) {
            $connectorConfiguration = clone $connectorDefinition->getConfiguration();
        } else {
            $connectorConfigurationClass = $connector->getConfigurationClass();
            $connectorConfiguration = new $connectorConfigurationClass();
        }

        try {
            $connectorConfiguration = $connector->processBackendConfiguration($connectorConfiguration, $configuration);
        } catch (\Throwable $e) {
            throw new \Exception(sprintf('Error while processing backend configuration for %s": %s', $connectorName, $e->getMessage()), 0, $e);
        }

        $connectorDefinition->setConfiguration($connectorConfiguration);

        $this->connectorDefinitionManager->update($connectorDefinition);
    }
}
