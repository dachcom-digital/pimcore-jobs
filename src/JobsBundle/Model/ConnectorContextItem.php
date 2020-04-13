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
     * @var int
     */
    protected $contextDefinitionId;

    /**
     * @var array
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
    public function setContextDefinitionId(int $contextDefinitionId)
    {
        $this->contextDefinitionId = $contextDefinitionId;
    }

    /**
     * {@inheritdoc}
     */
    public function getContextDefinitionId()
    {
        return $this->contextDefinitionId;
    }

    /**
     * {@inheritdoc}
     */
    public function setContextDefinition(array $contextDefinition)
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
}
