<?php

namespace JobsBundle\Model;

interface ConnectorContextItemInterface
{
    /**
     * @return int
     */
    public function getId();

    /**
     * @param int $objectId
     */
    public function setObjectId(int $objectId);

    /**
     * @return int
     */
    public function getObjectId();

    /**
     * @param ConnectorEngineInterface $connectorEngine
     */
    public function setConnectorEngine(ConnectorEngineInterface $connectorEngine);

    /**
     * @return ConnectorEngineInterface
     */
    public function getConnectorEngine();

    /**
     * @param int $contextDefinitionId
     */
    public function setContextDefinitionId(int $contextDefinitionId);

    /**
     * @return int
     */
    public function getContextDefinitionId();

    /**
     * @param array $contextDefinition
     */
    public function setContextDefinition(array $contextDefinition);

    /**
     * @return array
     */
    public function getContextDefinition();
}
