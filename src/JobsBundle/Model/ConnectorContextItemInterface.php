<?php

namespace JobsBundle\Model;

interface ConnectorContextItemInterface
{
    public function setId(?int $id): void;

    public function getId(): ?int;

    public function setObjectId(?int $objectId): void;

    public function getObjectId(): ?int;

    public function setConnectorEngine(ConnectorEngineInterface $connectorEngine): void;

    public function getConnectorEngine(): ?ConnectorEngineInterface;

    public function setContextDefinition(ContextDefinitionInterface $contextDefinition): void;

    public function getContextDefinition(): ?ContextDefinitionInterface;
}
