<?php

namespace JobsBundle\Model;

use JobsBundle\Connector\JobsConnectorConfigurationInterface;

interface ConnectorDefinitionInterface
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
     * @param string $token
     */
    public function setToken(string $token);

    /**
     * @return string
     */
    public function getToken();

    /**
     * @param JobsConnectorConfigurationInterface $configuration
     */
    public function setConfiguration(JobsConnectorConfigurationInterface $configuration);

    /**
     * @return JobsConnectorConfigurationInterface
     */
    public function getConfiguration();
}
