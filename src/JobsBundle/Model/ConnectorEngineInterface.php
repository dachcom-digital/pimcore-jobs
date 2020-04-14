<?php

namespace JobsBundle\Model;

use JobsBundle\Connector\ConnectorEngineConfigurationInterface;

interface ConnectorEngineInterface
{
    /**
     * @return int
     */
    public function getId();

    /**
     * @param string $name
     */
    public function setName(string $name);

    /**
     * @return string
     */
    public function getName();

    /**
     * @param bool $enabled
     */
    public function setEnabled(bool $enabled);

    /**
     * @return bool
     */
    public function getEnabled();

    /**
     * @return bool
     */
    public function isEnabled();

    /**
     * @param array $feedIds
     */
    public function setFeedIds(array $feedIds);

    /**
     * @return bool
     */
    public function hasFeedIds();

    /**
     * @return array|null
     */
    public function getFeedIds();

    /**
     * @param string $token
     */
    public function setToken(string $token);

    /**
     * @return string
     */
    public function getToken();

    /**
     * @param ConnectorEngineConfigurationInterface $configuration
     */
    public function setConfiguration(ConnectorEngineConfigurationInterface $configuration);

    /**
     * @return ConnectorEngineConfigurationInterface
     */
    public function getConfiguration();
}
