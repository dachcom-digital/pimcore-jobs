<?php

namespace JobsBundle\Connector\Google;

use JobsBundle\Connector\ConnectorDefinitionInterface;
use JobsBundle\Connector\ConnectorEngineConfigurationInterface;
use JobsBundle\Feed\FeedGeneratorInterface;
use JobsBundle\Model\ConnectorEngineInterface;
use JobsBundle\Transformer\ItemTransformerInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ConnectorDefinition implements ConnectorDefinitionInterface
{
    protected bool $dependenciesInstalled;
    protected ?ConnectorEngineInterface $connectorEngine;
    protected array $definitionConfiguration;
    protected ItemTransformerInterface $itemTransformer;

    public function __construct(bool $dependenciesInstalled)
    {
        $this->dependenciesInstalled = $dependenciesInstalled;
    }

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
        $resolver->setDefaults([
            'core_disabled' => false
        ]);

        $resolver->setAllowedTypes('core_disabled', 'bool');

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
        if ($this->definitionConfiguration['core_disabled'] === true) {
            return false;
        }

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
        if ($this->dependenciesInstalled === false) {
            $message = 'Dependencies not found. To enable this connector you need to install and activate "dachcom-digital/seo" and "dachcom-digital/schema"';

            throw new \Exception($message);
        }
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
        return new FeedGenerator($this->itemTransformer, $items, $params);
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
