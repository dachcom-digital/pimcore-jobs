<?php

namespace JobsBundle\Connector;

use JobsBundle\Model\ConnectorDefinitionInterface;

interface ConnectorServiceInterface
{
    /**
     * @param string $connectorName
     *
     * @return bool
     */
    public function connectorIsInstalled(string $connectorName);

    /**
     * @param string $connectorName
     *
     * @return bool
     */
    public function connectorIsEnabled(string $connectorName);

    /**
     * @param string $connectorName
     *
     * @return bool
     */
    public function connectorIsConnected(string $connectorName);

    /**
     * @param string $connectorName
     *
     * @return bool
     */
    public function connectorHasDataFeed(string $connectorName);

    /**
     * @param string $connectorName
     *
     * @return ConnectorDefinitionInterface
     */
    public function installConnector(string $connectorName);

    /**
     * @param string $connectorName
     *
     * @return void
     */
    public function uninstallConnector(string $connectorName);

    /**
     * @param string $connectorName
     *
     * @return void
     */
    public function enableConnector(string $connectorName);

    /**
     * @param string $connectorName
     *
     * @return void
     */
    public function disableConnector(string $connectorName);

    /**
     * @param string $connectorName
     */
    public function connectConnector(string $connectorName);

    /**
     * @param string $connectorName
     */
    public function disconnectConnector(string $connectorName);

    /**
     * @param string $connectorName
     *
     * @return string
     */
    public function getConnectorToken(string $connectorName);

    /**
     * @param string $connectorName
     *
     * @return bool
     */
    public function connectorHasCustomConfig(string $connectorName);

    /**
     * @param string $connectorName
     *
     * @return JobsConnectorConfigurationInterface|null
     */
    public function getConnectorConfiguration(string $connectorName);

    /**
     * @param string $connectorName
     *
     * @return array
     */
    public function getConnectorConfigurationForBackend(string $connectorName);

    /**
     * @param string                              $connectorName
     * @param JobsConnectorConfigurationInterface $connectorConfiguration
     */
    public function updateConnectorConfiguration(string $connectorName, JobsConnectorConfigurationInterface $connectorConfiguration);

    /**
     * @param string     $connectorName
     * @param array|null $configuration
     */
    public function updateConnectorConfigurationFromArray(string $connectorName, ?array $configuration);
}
