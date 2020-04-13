<?php

namespace JobsBundle\Connector\Google;

use JobsBundle\Connector\ConnectorDefinitionInterface;
use JobsBundle\Model\ConnectorEngineInterface;
use JobsBundle\Transformer\ItemTransformerInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ConnectorDefinition implements ConnectorDefinitionInterface
{
    /**
     * @var bool
     */
    protected $dependenciesInstalled;

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
     * @param bool $dependenciesInstalled
     */
    public function __construct(bool $dependenciesInstalled)
    {
        $this->dependenciesInstalled = $dependenciesInstalled;
    }

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
            throw new \Exception(sprintf('Invalid "%s" connector configuration. %s', 'google', $e->getMessage()));
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
        if ($this->dependenciesInstalled === false) {
            $message = 'Dependencies not found. To enable this connector you need to install and activate "dachcom-digital/seo" and "dachcom-digital/schema"';
            throw new \Exception($message);
        }
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
        return false;
    }

    /**
     * {@inheritDoc}
     */
    public function allowMultipleContextItems()
    {
        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function isAutoConnected()
    {
        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function isConnected()
    {
        return $this->isAutoConnected();
    }

    /**
     * {@inheritDoc}
     */
    public function connect()
    {
        // not required. this module is auto connected.
    }

    /**
     * {@inheritDoc}
     */
    public function disconnect()
    {
        // not required. this module is auto connected.
    }

    /**
     * {@inheritDoc}
     */
    public function buildFeedGenerator(array $items, array $params)
    {
        return new FeedGenerator($this->itemTransformer, $items, $params);
    }

    public function getDefinitionConfiguration()
    {
        return $this->definitionConfiguration;
    }

    /**
     * {@inheritDoc}
     */
    public function needsEngineConfiguration()
    {
        return false;
    }

    /**
     * {@inheritDoc}
     */
    public function getEngineConfigurationClass()
    {
        return null;
    }

    /**
     * {@inheritDoc}
     */
    public function getEngineConfiguration()
    {
        return null;
    }

    /**
     * {@inheritDoc}
     */
    public function mapEngineConfigurationFromBackend(array $data)
    {
        return null;
    }

}
