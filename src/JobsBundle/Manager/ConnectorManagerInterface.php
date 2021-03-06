<?php

namespace JobsBundle\Manager;

use JobsBundle\Connector\ConnectorDefinitionInterface;
use JobsBundle\Model\ConnectorEngineInterface;

interface ConnectorManagerInterface
{
    /**
     * @param string $connectorDefinitionName
     *
     * @return bool
     */
    public function connectorDefinitionIsEnabled(string $connectorDefinitionName);

    /**
     * @param bool $loadEngine
     *
     * @return ConnectorDefinitionInterface[]
     */
    public function getAllConnectorDefinitions(bool $loadEngine = false);

    /**
     * @param string $connectorDefinitionName
     * @param bool   $loadEngine
     *
     * @return ConnectorDefinitionInterface|null
     */
    public function getConnectorDefinition(string $connectorDefinitionName, bool $loadEngine = false);

    /**
     * @param int $id
     *
     * @return ConnectorEngineInterface|null
     */
    public function getEngineById(int $id);

    /**
     * @param string $connectorName
     *
     * @return ConnectorEngineInterface|null
     */
    public function getEngineByName(string $connectorName);

    /**
     * @param string $connectorName
     * @param null   $token
     * @param bool   $persist
     *
     * @return ConnectorEngineInterface
     */
    public function createNewEngine(string $connectorName, $token = null, bool $persist = true);

    /**
     * @param ConnectorEngineInterface $connector
     *
     * @return ConnectorEngineInterface|null
     */
    public function updateEngine(ConnectorEngineInterface $connector);

    /**
     * @param ConnectorEngineInterface $connector
     */
    public function deleteEngine(ConnectorEngineInterface $connector);

    /**
     * @param string $connectorName
     */
    public function deleteEngineByName(string $connectorName);
}
