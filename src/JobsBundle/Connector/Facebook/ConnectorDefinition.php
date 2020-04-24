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
            throw new \Exception(sprintf('Invalid "%s" connector configuration. %s', 'facebook', $e->getMessage()));
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
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function isAutoConnected()
    {
        return false;
    }

    /**
     * {@inheritdoc}
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
     * {@inheritdoc}
     */
    public function connect()
    {
        if ($this->isConnected() === false) {
            throw new \Exception('No valid Access Token found. If you already tried to connect your application check your credentials again.');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function disconnect()
    {
        // @todo
    }

    /**
     * {@inheritdoc}
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
     * {@inheritdoc}
     */
    public function getDefinitionConfiguration()
    {
        return $this->definitionConfiguration;
    }

    /**
     * {@inheritdoc}
     */
    public function needsEngineConfiguration()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function getEngineConfigurationClass()
    {
        return EngineConfiguration::class;
    }

    /**
     * {@inheritdoc}
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
     * {@inheritdoc}
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
