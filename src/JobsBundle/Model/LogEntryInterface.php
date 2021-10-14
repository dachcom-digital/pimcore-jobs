<?php

namespace JobsBundle\Model;

interface LogEntryInterface
{
    public function getId(): ?int;

    public function setConnectorEngine(ConnectorEngineInterface $connectorEngine): void;

    public function getConnectorEngine(): ConnectorEngineInterface;

    public function setObjectId(int $objectId): void;

    public function getObjectId(): int;

    public function getType(): string;

    public function setType(string $type): void;

    public function getMessage(): string;

    public function setMessage(string $message): void;

    public function getCreationDate();

    public function setCreationDate(\DateTime $date): void;
}
