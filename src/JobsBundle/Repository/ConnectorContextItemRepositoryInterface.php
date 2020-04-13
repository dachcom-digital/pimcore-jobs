<?php

namespace JobsBundle\Repository;

use JobsBundle\Model\ConnectorContextItemInterface;

interface ConnectorContextItemRepositoryInterface
{
    /**
     * @param int $id
     *
     * @return null|ConnectorContextItemInterface
     */
    public function findById(int $id);

    /**
     * @param int $objectId
     *
     * @return ConnectorContextItemInterface[]
     */
    public function findForObject(int $objectId);

    /**
     * @param int $connectorEngineId
     *
     * @return ConnectorContextItemInterface[]
     */
    public function findForConnectorEngine(int $connectorEngineId);

    /**
     * @param int $connectorEngineId
     * @param int    $objectId
     *
     * @return ConnectorContextItemInterface[]
     */
    public function findForConnectorEngineAndObject(int $connectorEngineId, int $objectId);
}
