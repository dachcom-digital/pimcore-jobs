<?php

namespace JobsBundle\Connector;

use JobsBundle\Context\ResolvedItemInterface;
use JobsBundle\Model\ConnectorEngineInterface;

interface ConnectorServiceInterface
{
    /**
     * @param string $connectorName
     *
     * @return ConnectorEngineInterface
     */
    public function installConnector(string $connectorName);

    /**
     * @param string $connectorName
     */
    public function uninstallConnector(string $connectorName);

    /**
     * @param string $connectorName
     */
    public function enableConnector(string $connectorName);

    /**
     * @param string $connectorName
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
     * @param string                        $connectorName
     * @param string                        $outputType
     * @param array|ResolvedItemInterface[] $items
     * @param array                         $params
     *
     * @return mixed|void
     */
    public function generateConnectorFeed(string $connectorName, string $outputType, array $items, array $params = []);

    /**
     * @param string $connectorName
     * @param array  $feedIds
     */
    public function updateConnectorFeedIds(string $connectorName, array $feedIds);

    /**
     * @param string                                $connectorName
     * @param ConnectorEngineConfigurationInterface $connectorConfiguration
     */
    public function updateConnectorEngineConfiguration(string $connectorName, ConnectorEngineConfigurationInterface $connectorConfiguration);

    /**
     * @param string $connectorDefinitionName
     *
     * @return bool
     */
    public function connectorDefinitionIsEnabled(string $connectorDefinitionName);

    /**
     * @param string $connectorDefinitionName
     * @param bool   $loadEngine
     *
     * @return ConnectorDefinitionInterface
     */
    public function getConnectorDefinition(string $connectorDefinitionName, bool $loadEngine = false);
}
