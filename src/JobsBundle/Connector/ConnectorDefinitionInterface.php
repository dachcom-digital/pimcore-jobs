<?php

namespace JobsBundle\Connector;

use JobsBundle\Feed\FeedGeneratorInterface;
use JobsBundle\Model\ConnectorEngineInterface;
use JobsBundle\Transformer\ItemTransformerInterface;

interface ConnectorDefinitionInterface
{
    public function setConnectorEngine(?ConnectorEngineInterface $connectorEngine): void;

    public function getConnectorEngine(): ?ConnectorEngineInterface;

    public function setItemTransformer(ItemTransformerInterface $itemTransformer): void;

    /**
     * @throws \Exception
     */
    public function setDefinitionConfiguration(array $definitionConfiguration): void;

    public function engineIsLoaded(): bool;

    /**
     * Returns true if connector is fully configured and ready to provide data.
     */
    public function isOnline(): bool;

    /**
     * @throws \Exception
     */
    public function beforeEnable(): void;

    /**
     * @throws \Exception
     */
    public function beforeDisable(): void;

    public function allowMultipleContextItems(): bool;

    public function isAutoConnected(): bool;

    public function isConnected(): bool;

    public function connect(): void;

    public function disconnect(): void;

    public function buildFeedGenerator(array $items, array $params): ?FeedGeneratorInterface;

    public function getDefinitionConfiguration(): array;

    public function needsEngineConfiguration(): bool;

    public function hasLogPanel(): bool;

    public function getEngineConfigurationClass(): ?string;

    public function getEngineConfiguration(): ?array;

    public function mapEngineConfigurationFromBackend(array $data): ?ConnectorEngineConfigurationInterface;
}
