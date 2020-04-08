<?php

namespace JobsBundle\Connector\Facebook;

use JobsBundle\Connector\JobsConnectorConfigurationInterface;
use JobsBundle\Connector\JobsConnectorInterface;

class Connector implements JobsConnectorInterface
{
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
    public function isConnected(?JobsConnectorConfigurationInterface $configuration)
    {
        if (!$configuration instanceof Configuration) {
            return false;
        }

        return $configuration->getAccessToken() !== null;
    }

    /**
     * {@inheritDoc}
     */
    public function connect(?JobsConnectorConfigurationInterface $configuration)
    {
        if ($this->isConnected($configuration) === false) {
            throw new \Exception('No valid Access Token found. If you already tried to connect your application check your credentials again.');
        }
    }

    /**
     * {@inheritDoc}
     */
    public function disconnect(?JobsConnectorConfigurationInterface $configuration)
    {
        // @todo
    }

    /**
     * {@inheritDoc}
     */
    public function getConfigurationClass()
    {
        return Configuration::class;
    }

    /**
     * {@inheritDoc}
     */
    public function processBackendConfiguration(JobsConnectorConfigurationInterface $configuration, $data)
    {
        if (!$configuration instanceof Configuration) {
            throw new \Exception('Configuration must be instance of %s, %s given.',
                Configuration::class,
                (is_object($configuration) ? get_class($configuration) : gettype($configuration)));
        }

        $configuration->setAppId($data['appId']);
        $configuration->setAppSecret($data['appSecret']);
        $configuration->setAccessToken(null);

        return $configuration;
    }
}
