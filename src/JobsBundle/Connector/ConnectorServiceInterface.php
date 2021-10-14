<?php

namespace JobsBundle\Connector;

use JobsBundle\Model\ConnectorEngineInterface;

interface ConnectorServiceInterface
{
    public function installConnector(string $connectorName): ConnectorEngineInterface;

    public function uninstallConnector(string $connectorName): void;

    public function enableConnector(string $connectorName): void;

    public function disableConnector(string $connectorName): void;

    public function connectConnector(string $connectorName): void;

    public function disconnectConnector(string $connectorName): void;

    public function generateConnectorFeed(string $connectorName, string $outputType, array $items, array $params = []): mixed;

    public function updateConnectorFeedIds(string $connectorName, array $feedIds): void;

    public function updateConnectorEngineConfiguration(string $connectorName, ConnectorEngineConfigurationInterface $connectorConfiguration): void;

    public function connectorDefinitionIsEnabled(string $connectorDefinitionName): bool;

    public function getConnectorDefinition(string $connectorDefinitionName, bool $loadEngine = false): ConnectorDefinitionInterface;
}
