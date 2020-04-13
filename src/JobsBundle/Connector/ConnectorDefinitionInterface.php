<?php

namespace JobsBundle\Connector;

use JobsBundle\Context\ResolvedItemInterface;
use JobsBundle\Feed\FeedGeneratorInterface;
use JobsBundle\Model\ConnectorEngineInterface;
use JobsBundle\Transformer\ItemTransformerInterface;

interface ConnectorDefinitionInterface
{
    /**
     * @param ConnectorEngineInterface|null $connectorEngine
     */
    public function setConnectorEngine(?ConnectorEngineInterface $connectorEngine);

    /**
     * @return ConnectorEngineInterface|null
     */
    public function getConnectorEngine();

    /**
     * @param ItemTransformerInterface $itemTransformer
     */
    public function setItemTransformer(ItemTransformerInterface $itemTransformer);

    /**
     * @param array $configuration
     *
     * @throws \Exception
     */
    public function setDefinitionConfiguration(array $configuration);

    /**
     * @return bool
     */
    public function engineIsLoaded();

    /**
     * @return bool
     */
    public function isOnline();

    /**
     * @throws \Exception
     */
    public function beforeEnable();

    /**
     * @throws \Exception
     */
    public function beforeDisable();

    /**
     * @return bool
     */
    public function hasDataFeed();

    /**
     * @return bool
     */
    public function allowMultipleContextItems();

    /**
     * @return bool
     */
    public function isAutoConnected();

    /**
     * @return bool
     */
    public function isConnected();

    /**
     *
     */
    public function connect();

    /**
     *
     */
    public function disconnect();

    /**
     * @param array|ResolvedItemInterface[] $items
     * @param array                         $params
     *
     * @return FeedGeneratorInterface
     */
    public function buildFeedGenerator(array $items, array $params);

    /**
     * @return array
     */
    public function getDefinitionConfiguration();

    /**
     * @return bool
     */
    public function needsEngineConfiguration();

    /**
     * @return null|string
     */
    public function getEngineConfigurationClass();

    /**
     * @return array|null
     */
    public function getEngineConfiguration();

    /**
     * @param array $data
     *
     * @return ConnectorEngineConfigurationInterface|null
     */
    public function mapEngineConfigurationFromBackend(array $data);

}

