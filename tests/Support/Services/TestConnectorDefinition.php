<?php

namespace DachcomBundle\Test\Support\Services;

use JobsBundle\Connector\ConnectorDefinitionInterface;
use JobsBundle\Connector\ConnectorEngineConfigurationInterface;
use JobsBundle\Feed\FeedGeneratorInterface;
use JobsBundle\Model\ConnectorEngineInterface;
use JobsBundle\Transformer\ItemTransformerInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TestConnectorDefinition implements ConnectorDefinitionInterface
{
    protected ?ConnectorEngineInterface $connectorEngine;
    protected array $definitionConfiguration;
    protected ItemTransformerInterface $itemTransformer;

    public function getConnectorEngine(): ?ConnectorEngineInterface
    {
        return $this->connectorEngine;
    }

    public function setConnectorEngine(?ConnectorEngineInterface $connectorEngine): void
    {
        $this->connectorEngine = $connectorEngine;
    }

    public function setItemTransformer(ItemTransformerInterface $itemTransformer): void
    {
        $this->itemTransformer = $itemTransformer;
    }

    public function setDefinitionConfiguration(array $definitionConfiguration): void
    {
        $resolver = new OptionsResolver();
        $resolver->setDefaults([]);

        try {
            $this->definitionConfiguration = $resolver->resolve($definitionConfiguration);
        } catch (\Throwable $e) {
            throw new \Exception(sprintf('Invalid "%s" connector configuration. %s', 'google', $e->getMessage()));
        }
    }

    public function engineIsLoaded(): bool
    {
        return $this->getConnectorEngine() instanceof ConnectorEngineInterface;
    }

    public function isOnline(): bool
    {
        if (!$this->engineIsLoaded()) {
            return false;
        }

        if ($this->getConnectorEngine()->isEnabled() === false) {
            return false;
        }

        return $this->isConnected();
    }

    public function beforeEnable(): void
    {
        // not required. just enable it.
    }

    public function beforeDisable(): void
    {
        // not required. just disable it.
    }

    public function allowMultipleContextItems(): bool
    {
        return true;
    }

    public function isAutoConnected(): bool
    {
        return true;
    }

    public function isConnected(): bool
    {
        return $this->isAutoConnected();
    }

    public function connect(): void
    {
        // not required. this module is auto connected.
    }

    public function disconnect(): void
    {
        // not required. this module is auto connected.
    }

    public function buildFeedGenerator(array $items, array $params): ?FeedGeneratorInterface
    {
        return null;
    }

    public function getDefinitionConfiguration(): array
    {
        return $this->definitionConfiguration;
    }

    public function needsEngineConfiguration(): bool
    {
        return false;
    }

    public function hasLogPanel(): bool
    {
        return true;
    }

    public function getEngineConfigurationClass(): ?string
    {
        return null;
    }

    public function getEngineConfiguration(): ?ConnectorEngineConfigurationInterface
    {
        return null;
    }

    public function mapEngineConfigurationFromBackend(array $data): ?ConnectorEngineConfigurationInterface
    {
        return null;
    }
}
