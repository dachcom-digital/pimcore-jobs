<?php

/*
 * This source file is available under two different licenses:
 *   - GNU General Public License version 3 (GPLv3)
 *   - DACHCOM Commercial License (DCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) DACHCOM.DIGITAL AG (https://www.dachcom-digital.com)
 * @license    GPLv3 and DCL
 */

namespace JobsBundle\Connector\Facebook;

use JobsBundle\Connector\ConnectorDefinitionInterface;
use JobsBundle\Connector\ConnectorEngineConfigurationInterface;
use JobsBundle\Feed\FeedGeneratorInterface;
use JobsBundle\Model\ConnectorEngineInterface;
use JobsBundle\Transformer\ItemTransformerInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ConnectorDefinition implements ConnectorDefinitionInterface
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
        $resolver->setDefaults([
            'core_disabled' => false
        ]);

        $resolver->setAllowedTypes('core_disabled', 'bool');

        try {
            $this->definitionConfiguration = $resolver->resolve($definitionConfiguration);
        } catch (\Throwable $e) {
            throw new \Exception(sprintf('Invalid "%s" connector configuration. %s', 'facebook', $e->getMessage()));
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

        if (!$this->isConnected()) {
            return false;
        }

        $configuration = $this->getConnectorEngine()->getConfiguration();
        if (!$configuration instanceof EngineConfiguration) {
            return false;
        }

        if (empty($configuration->getRecruitingManagerId())) {
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
        return false;
    }

    public function isAutoConnected(): bool
    {
        return false;
    }

    public function isConnected(): bool
    {
        if ($this->engineIsLoaded() === false) {
            return false;
        }

        $configuration = $this->getConnectorEngine()->getConfiguration();
        if (!$configuration instanceof EngineConfiguration) {
            return false;
        }

        return $configuration->getAccessToken() !== null;
    }

    public function connect(): void
    {
        if ($this->isConnected() === false) {
            throw new \Exception('No valid Access Token found. If you already tried to connect your application check your credentials again.');
        }
    }

    public function disconnect(): void
    {
        // @todo
    }

    public function buildFeedGenerator(array $items, array $params): ?FeedGeneratorInterface
    {
        if ($this->engineIsLoaded() === false) {
            return null;
        }

        $configuration = $this->getConnectorEngine()->getConfiguration();

        $feedParams = [
            'publisherName' => $configuration->getConfigParam('publisherName'),
            'publisherUrl'  => $configuration->getConfigParam('publisherUrl'),
        ];

        return new FeedGenerator($this->itemTransformer, $items, $feedParams);
    }

    public function getDefinitionConfiguration(): array
    {
        return $this->definitionConfiguration;
    }

    public function needsEngineConfiguration(): bool
    {
        return true;
    }

    public function hasLogPanel(): bool
    {
        return false;
    }

    public function getEngineConfigurationClass(): ?string
    {
        return EngineConfiguration::class;
    }

    public function getEngineConfiguration(): ?ConnectorEngineConfigurationInterface
    {
        if (!$this->engineIsLoaded()) {
            return null;
        }

        $engineConfiguration = $this->getConnectorEngine()->getConfiguration();
        if (!$engineConfiguration instanceof ConnectorEngineConfigurationInterface) {
            return null;
        }

        return $engineConfiguration;
    }

    public function mapEngineConfigurationFromBackend(array $data): ?ConnectorEngineConfigurationInterface
    {
        $engine = $this->getConnectorEngine();
        if (!$engine instanceof ConnectorEngineInterface) {
            return null;
        }

        if ($engine->getConfiguration() instanceof ConnectorEngineConfigurationInterface) {
            $connectorConfiguration = clone $engine->getConfiguration();
        } else {
            $connectorEngineConfigurationClass = $this->getEngineConfigurationClass();
            $connectorConfiguration = new $connectorEngineConfigurationClass();
        }

        if (!$connectorConfiguration instanceof EngineConfiguration) {
            return null;
        }

        $connectorConfiguration->setAppId($data['appId']);
        $connectorConfiguration->setAppSecret($data['appSecret']);
        $connectorConfiguration->setPublisherName($data['publisherName']);
        $connectorConfiguration->setPublisherUrl($data['publisherUrl']);
        $connectorConfiguration->setPhotoUrl($data['photoUrl']);
        $connectorConfiguration->setDataPolicyUrl($data['dataPolicyUrl']);

        return $connectorConfiguration;
    }
}
