<?php

namespace JobsBundle\Manager;

use JobsBundle\Model\ConnectorContextItemInterface;
use JobsBundle\Model\ContextDefinitionInterface;

interface ConnectorContextManagerInterface
{
    /**
     * @return array<int, ConnectorContextItemInterface>
     */
    public function getForObject(int $objectId): array;

    /**
     * @return array<int, ConnectorContextItemInterface>
     */
    public function getForConnectorEngine(int $connectorEngineId): array;

    /**
     * @return array<int, ConnectorContextItemInterface>
     */
    public function getForConnectorEngineAndObject(int $connectorEngineId, int $objectId): array;

    public function getContextDefinition(int $definitionContextId): ContextDefinitionInterface;

    public function connectorAllowsMultipleContextItems(string $connectorDefinitionName): bool;

    public function createNew(int $connectorId): ConnectorContextItemInterface;

    public function update(ConnectorContextItemInterface $connectorContextItem): ConnectorContextItemInterface;

    public function delete(ConnectorContextItemInterface $connectorContextItem): void;

    public function generateConnectorContextConfig(array $connectorContextItems): array;
}
