<?php

namespace JobsBundle\Manager;

use JobsBundle\Connector\ConnectorDefinitionInterface;
use JobsBundle\Model\ConnectorEngineInterface;

interface ConnectorManagerInterface
{
    public function connectorDefinitionIsEnabled(string $connectorDefinitionName): bool;

    /**
     * @return array<int, ConnectorDefinitionInterface>
     */
    public function getAllConnectorDefinitions(bool $loadEngine = false): array;

    public function getConnectorDefinition(string $connectorDefinitionName, bool $loadEngine = false): ?ConnectorDefinitionInterface;

    public function getEngineById(int $id): ?ConnectorEngineInterface;

    public function getEngineByName(string $connectorName): ?ConnectorEngineInterface;

    public function createNewEngine(string $connectorName, $token = null, bool $persist = true): ConnectorEngineInterface;

    public function updateEngine(ConnectorEngineInterface $connector): ?ConnectorEngineInterface;

    public function deleteEngine(ConnectorEngineInterface $connector): void;

    public function deleteEngineByName(string $connectorName): void;
}
