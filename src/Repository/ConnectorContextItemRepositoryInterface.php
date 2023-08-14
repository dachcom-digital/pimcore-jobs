<?php

namespace JobsBundle\Repository;

use JobsBundle\Model\ConnectorContextItemInterface;

interface ConnectorContextItemRepositoryInterface
{
    public function findById(int $id): ?ConnectorContextItemInterface;

    /**
     * @return array<int, ConnectorContextItemInterface>
     */
    public function findForObject(int $objectId): array;

    /**
     * @return array<int, ConnectorContextItemInterface>
     */
    public function findForConnectorEngine(int $connectorEngineId);

    /**
     * @return array<int, ConnectorContextItemInterface>
     */
    public function findForConnectorEngineAndObject(int $connectorEngineId, int $objectId);
}
