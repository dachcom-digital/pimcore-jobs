<?php

namespace DachcomBundle\Test\Services;

use JobsBundle\Connector\ConnectorDefinitionInterface;
use JobsBundle\Model\ConnectorEngineInterface;
use JobsBundle\Transformer\ItemTransformerInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TestConnectorDefinition implements ConnectorDefinitionInterface
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
     * {@inheritdoc}
     */
    public function getConnectorEngine()
    {
        return $this->connectorEngine;
    }

    /**
     * {@inheritdoc}
     */
    public function setConnectorEngine(?ConnectorEngineInterface $connectorEngine)
    {
        $this->connectorEngine = $connectorEngine;
    }

    /**
     * {@inheritdoc}
     */
    public function setItemTransformer(ItemTransformerInterface $itemTransformer)
    {
        $this->itemTransformer = $itemTransformer;
    }

    /**
     * {@inheritdoc}
     */
    public function setDefinitionConfiguration(array $definitionConfiguration)
    {
        $resolver = new OptionsResolver();
        $resolver->setDefaults([]);

        try {
            $this->definitionConfiguration = $resolver->resolve($definitionConfiguration);
        } catch (\Throwable $e) {
            throw new \Exception(sprintf('Invalid "%s" connector configuration. %s', 'google', $e->getMessage()));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function engineIsLoaded()
    {
        return $this->getConnectorEngine() instanceof ConnectorEngineInterface;
    }

    /**
     * {@inheritdoc}
     */
    public function isOnline()
    {
        if (!$this->engineIsLoaded()) {
            return false;
        }

        if ($this->getConnectorEngine()->isEnabled() === false) {
            return false;
        }

        return $this->isConnected();
    }

    /**
     * {@inheritdoc}
     */
    public function beforeEnable()
    {
        // not required. just enable it.
    }

    /**
     * {@inheritdoc}
     */
    public function beforeDisable()
    {
        // not required. just disable it.
    }

    /**
     * {@inheritdoc}
     */
    public function allowMultipleContextItems()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isAutoConnected()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isConnected()
    {
        return $this->isAutoConnected();
    }

    /**
     * {@inheritdoc}
     */
    public function connect()
    {
        // not required. this module is auto connected.
    }

    /**
     * {@inheritdoc}
     */
    public function disconnect()
    {
        // not required. this module is auto connected.
    }

    /**
     * {@inheritdoc}
     */
    public function buildFeedGenerator(array $items, array $params)
    {
        return new \stdClass();
    }

    public function getDefinitionConfiguration()
    {
        return $this->definitionConfiguration;
    }

    /**
     * {@inheritdoc}
     */
    public function needsEngineConfiguration()
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function hasLogPanel()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function getEngineConfigurationClass()
    {
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getEngineConfiguration()
    {
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function mapEngineConfigurationFromBackend(array $data)
    {
        return null;
    }
}
