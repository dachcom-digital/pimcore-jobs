<?php

namespace JobsBundle\Manager;

use JobsBundle\Model\ConnectorContextItemInterface;

interface ConnectorContextManagerInterface
{
    /**
     * @param int $objectId
     *
     * @return ConnectorContextItemInterface[]
     */
    public function getForObject(int $objectId);

    /**
     * @param int $connectorEngineId
     *
     * @return ConnectorContextItemInterface[]
     */
    public function getForConnectorEngine(int $connectorEngineId);

    /**
     * @param int $connectorEngineId
     * @param int $objectId
     *
     * @return ConnectorContextItemInterface[]
     */
    public function getForConnectorEngineAndObject(int $connectorEngineId, int $objectId);

    /**
     * @param int  $connectorId
     * @param bool $persist
     *
     * @return mixed
     */
    public function createNew(int $connectorId, bool $persist = true);

    /**
     * @param ConnectorContextItemInterface $connectorContextItem
     *
     * @return ConnectorContextItemInterface
     */
    public function update(ConnectorContextItemInterface $connectorContextItem);

    /**
     * @param ConnectorContextItemInterface $connectorContextItem
     */
    public function delete(ConnectorContextItemInterface $connectorContextItem);

    /**
     * @param int $objectId
     *
     * @return ConnectorContextItemInterface[]
     */
    public function generateConnectorContextConfigForObject(int $objectId);

}
