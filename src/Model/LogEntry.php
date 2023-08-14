<?php

namespace JobsBundle\Model;

class LogEntry implements LogEntryInterface
{
    protected ?int $id = null;
    protected ConnectorEngineInterface $connectorEngine;
    protected int $objectId;
    protected string $type;
    protected string $message;
    protected \DateTime $creationDate;

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setConnectorEngine(ConnectorEngineInterface $connectorEngine): void
    {
        $this->connectorEngine = $connectorEngine;
    }

    public function getConnectorEngine(): ConnectorEngineInterface
    {
        return $this->connectorEngine;
    }

    public function setObjectId(int $objectId): void
    {
        $this->objectId = $objectId;
    }

    public function getObjectId(): int
    {
        return $this->objectId;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): void
    {
        $this->type = $type;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function setMessage(string $message): void
    {
        $this->message = $message;
    }

    public function setCreationDate(\DateTime $date): void
    {
        $this->creationDate = $date;
    }

    public function getCreationDate(): \DateTime
    {
        return $this->creationDate;
    }
}
