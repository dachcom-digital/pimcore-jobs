<?php

namespace JobsBundle\Connector\Google;

use JobsBundle\Connector\JobsConnectorConfigurationInterface;
use JobsBundle\Connector\JobsConnectorInterface;

class Connector implements JobsConnectorInterface
{
    /**
     * {@inheritDoc}
     */
    public function beforeEnable()
    {
        if (class_exists('JobsBundle\JobsBundle')) {
            return;
        }

        throw new \Exception('Depending JobsBundle not found. Please install it via "composer require dachcom-digital/jobs:^1.0"');
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
    public function isAutoConnected()
    {
        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function isConnected(?JobsConnectorConfigurationInterface $configuration)
    {
        return $this->isAutoConnected();
    }

    /**
     * {@inheritDoc}
     */
    public function connect(?JobsConnectorConfigurationInterface $configuration)
    {
        // not required. this module is auto connected.
    }

    /**
     * {@inheritDoc}
     */
    public function disconnect(?JobsConnectorConfigurationInterface $configuration)
    {
        // not required. this module is auto connected.
    }

    /**
     * {@inheritDoc}
     */
    public function getConfigurationClass()
    {
        return null;
    }

    /**
     * {@inheritDoc}
     */
    public function processBackendConfiguration(JobsConnectorConfigurationInterface $configuration, $data)
    {
        return null;
    }
}
