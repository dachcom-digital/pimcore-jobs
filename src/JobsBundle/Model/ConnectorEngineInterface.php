<?php

namespace JobsBundle\Model;

use JobsBundle\Connector\ConnectorEngineConfigurationInterface;

interface ConnectorEngineInterface
{
    public function getId(): ?int;

    public function setName(string $name): void;

    public function getName(): string;

    public function setEnabled(bool $enabled): void;

    public function getEnabled(): bool;

    public function isEnabled(): bool;

    public function setFeedIds(array $feedIds): void;

    public function hasFeedIds(): bool;

    public function getFeedIds(): ?array;

    public function setToken(string $token): void;

    public function getToken(): string;

    public function setConfiguration(ConnectorEngineConfigurationInterface $configuration): void;

    public function getConfiguration(): ConnectorEngineConfigurationInterface;

    public function isFromClone(): bool;
}
