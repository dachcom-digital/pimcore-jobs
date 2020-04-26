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
     * {@inheritdoc}
     */
    public function beforeEnable()
    {
        if ($this->dependenciesInstalled === false) {
            $message = 'Dependencies not found. To enable this connector you need to install and activate "dachcom-digital/seo" and "dachcom-digital/schema"';

            throw new \Exception($message);
        }
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
        return new FeedGenerator($this->itemTransformer, $items, $params);
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
