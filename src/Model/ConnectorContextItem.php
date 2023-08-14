<?php

namespace JobsBundle\Model;

class ConnectorContextItem implements ConnectorContextItemInterface
{
    protected ?int $id = null;
    protected ?int $objectId = null;
    protected ?ConnectorEngineInterface $connectorEngine;
    protected ?ContextDefinitionInterface $contextDefinition;

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setObjectId(?int $objectId): void
    {
        $this->objectId = $objectId;
    }

    public function getObjectId(): ?int
    {
        return $this->objectId;
    }

    public function setConnectorEngine(ConnectorEngineInterface $connectorEngine): void
    {
        $this->connectorEngine = $connectorEngine;
    }

    public function getConnectorEngine(): ?ConnectorEngineInterface
    {
        return $this->connectorEngine;
    }

    public function setContextDefinition(ContextDefinitionInterface $contextDefinition): void
    {
        $this->contextDefinition = $contextDefinition;
    }

    public function getContextDefinition(): ?ContextDefinitionInterface
    {
        return $this->contextDefinition;
    }

    public function __clone()
    {
        $this->id = null;
    }
}
