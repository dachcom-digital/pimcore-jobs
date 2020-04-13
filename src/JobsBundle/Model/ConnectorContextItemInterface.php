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
     * @param ContextDefinitionInterface $contextDefinition
     */
    public function setContextDefinition(ContextDefinitionInterface $contextDefinition);

    /**
     * @return ContextDefinitionInterface
     */
    public function getContextDefinition();
}
