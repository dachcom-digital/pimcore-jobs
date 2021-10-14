<?php

namespace JobsBundle\Model;

use JobsBundle\Connector\ConnectorEngineConfigurationInterface;

class ConnectorEngine implements ConnectorEngineInterface
{
    protected ?int $id;
    protected string $name;
    protected bool $enabled;
    protected string $token;
    protected ConnectorEngineConfigurationInterface $configuration;
    protected ?array $feedIds;
    protected bool $fromClone;

    public function setId($id): void
    {
        $this->id = $id;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setEnabled(bool $enabled): void
    {
        $this->enabled = $enabled;
    }

    public function getEnabled(): bool
    {
        return $this->enabled;
    }

    public function setToken(string $token): void
    {
        $this->token = $token;
    }

    public function getToken(): string
    {
        return $this->token;
    }

    public function isEnabled(): bool
    {
        return $this->enabled === true;
    }

    public function setFeedIds(array $feedIds): void
    {
        $this->feedIds = $feedIds;
    }

    public function hasFeedIds(): bool
    {
        return is_array($this->feedIds) && count($this->feedIds) > 0;
    }

    public function getFeedIds(): ?array
    {
        return $this->feedIds;
    }

    public function setConfiguration(ConnectorEngineConfigurationInterface $configuration): void
    {
        $this->configuration = $configuration;
    }

    public function getConfiguration(): ConnectorEngineConfigurationInterface
    {
        return $this->configuration;
    }

    public function isFromClone(): bool
    {
        return $this->fromClone;
    }

    public function __clone(): void
    {
        $this->fromClone = true;
    }
}
