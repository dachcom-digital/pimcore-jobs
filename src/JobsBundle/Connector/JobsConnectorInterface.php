<?php

namespace JobsBundle\Connector;

interface JobsConnectorInterface
{
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
    public function isAutoConnected();

    /**
     * @param JobsConnectorConfigurationInterface|null $configuration
     *
     * @return bool
     */
    public function isConnected(?JobsConnectorConfigurationInterface $configuration);

    /**
     * @param JobsConnectorConfigurationInterface $configuration
     */
    public function connect(JobsConnectorConfigurationInterface $configuration);

    /**
     * @param JobsConnectorConfigurationInterface|null $configuration
     */
    public function disconnect(?JobsConnectorConfigurationInterface $configuration);

    /**
     * @return null|string
     */
    public function getConfigurationClass();

    /**
     * @param JobsConnectorConfigurationInterface $configuration
     * @param array                               $data
     *
     * @return JobsConnectorConfigurationInterface
     */
    public function processBackendConfiguration(JobsConnectorConfigurationInterface $configuration, $data);

}

