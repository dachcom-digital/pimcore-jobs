<?php

namespace JobsBundle\Connector\Facebook;

use JobsBundle\Connector\ConnectorEngineConfigurationInterface;
use JobsBundle\Connector\ConnectorDefinitionInterface;
use JobsBundle\Model\ConnectorEngineInterface;
use JobsBundle\Transformer\ItemTransformerInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ConnectorDefinition implements ConnectorDefinitionInterface
{
    /**
     * @var ConnectorEngineInterface|null
     */
    protected $connectorEngine;

    /**
     * @var array
     */
    protected $definitionConfiguration;

    /**
     * @var ItemTransformerInterface
     */
    protected $itemTransformer;

    /**
     * {@inheritDoc}
     */
    public function getConnectorEngine()
    {
        return $this->connectorEngine;
    }

    /**
     * {@inheritDoc}
     */
    public function setConnectorEngine(?ConnectorEngineInterface $connectorEngine)
    {
        $this->connectorEngine = $connectorEngine;
    }

    /**
     * {@inheritDoc}
     */
    public function setItemTransformer(ItemTransformerInterface $itemTransformer)
    {
        $this->itemTransformer = $itemTransformer;
    }

    /**
     * {@inheritDoc}
     */
    public function setDefinitionConfiguration(array $definitionConfiguration)
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

    /**
     * {@inheritDoc}
     */
    public function engineIsLoaded()
    {
        return $this->getConnectorEngine() instanceof ConnectorEngineInterface;
    }

    /**
     * {@inheritDoc}
     */
    public function isOnline()
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

    /**
     * {@inheritDoc}
     */
    public function beforeEnable()
    {
        // not required. just enable it.
    }

    /**
     * {@inheritDoc}
     */
    public function beforeDisable()
    {
        // not required. just disable it.
    }

    /**
     * {@inheritDoc}
     */
    public function hasDataFeed()
    {
        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function isAutoConnected()
    {
        return false;
    }

    /**
     * {@inheritDoc}
     */
    public function isConnected()
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

    /**
     * {@inheritDoc}
     */
    public function connect()
    {
        if ($this->isConnected() === false) {
            throw new \Exception('No valid Access Token found. If you already tried to connect your application check your credentials again.');
        }
    }

    /**
     * {@inheritDoc}
     */
    public function disconnect()
    {
        // @todo
    }

    /**
     * {@inheritDoc}
     */
    public function buildFeedGenerator(array $items, array $params)
    {
        if ($this->engineIsLoaded() === false) {
            return false;
        }

        $configuration = $this->getConnectorEngine()->getConfiguration();

        $params = [
            'publisherName' => $configuration->getConfigParam('publisherName'),
            'publisherUrl'  => $configuration->getConfigParam('publisherUrl'),
        ];

        return new FeedGenerator($this->itemTransformer, $items, $params);
    }

    /**
     * {@inheritDoc}
     */
    public function getDefinitionConfiguration()
    {
        return $this->definitionConfiguration;
    }

    /**
     * {@inheritDoc}
     */
    public function needsEngineConfiguration()
    {
        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function getEngineConfigurationClass()
    {
        return EngineConfiguration::class;
    }

    /**
     * {@inheritDoc}
     */
    public function getEngineConfiguration()
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

    /**
     * {@inheritDoc}
     */
    public function mapEngineConfigurationFromBackend(array $data)
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

        $connectorConfiguration->setAppId($data['appId']);
        $connectorConfiguration->setAppSecret($data['appSecret']);
        $connectorConfiguration->setPublisherUrl($data['publisherUrl']);
        $connectorConfiguration->setPublisherName($data['publisherName']);

        return $connectorConfiguration;
    }
}
