<?php

namespace JobsBundle\Model;

class ConnectorContextItem implements ConnectorContextItemInterface
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var int
     */
    protected $objectId;

    /**
     * @var ConnectorEngineInterface
     */
    protected $connectorEngine;

    /**
     * @var ContextDefinitionInterface
     */
    protected $contextDefinition;

    /**
     * {@inheritdoc}
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function setObjectId(int $objectId)
    {
        $this->objectId = $objectId;
    }

    /**
     * {@inheritdoc}
     */
    public function getObjectId()
    {
        return $this->objectId;
    }

    /**
     * {@inheritdoc}
     */
    public function setConnectorEngine(ConnectorEngineInterface $connectorEngine)
    {
        $this->connectorEngine = $connectorEngine;
    }

    /**
     * {@inheritdoc}
     */
    public function getConnectorEngine()
    {
        return $this->connectorEngine;
    }

    /**
     * {@inheritdoc}
     */
    public function setContextDefinition(ContextDefinitionInterface $contextDefinition)
    {
        $this->contextDefinition = $contextDefinition;
    }

    /**
     * {@inheritdoc}
     */
    public function getContextDefinition()
    {
        return $this->contextDefinition;
    }

    public function __clone()
    {
        $this->id = null;
    }
}
