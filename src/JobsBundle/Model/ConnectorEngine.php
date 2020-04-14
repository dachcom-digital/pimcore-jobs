<?php

namespace JobsBundle\Model;

use JobsBundle\Connector\ConnectorEngineConfigurationInterface;

class ConnectorEngine implements ConnectorEngineInterface
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var bool
     */
    protected $enabled;

    /**
     * @var string
     */
    protected $token;

    /**
     * @var array
     */
    protected $configuration;

    /**
     * @var array
     */
    protected $feedIds;

    /**
     * {@inheritdoc}
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function setName(string $name)
    {
        $this->name = $name;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function setEnabled(bool $enabled)
    {
        $this->enabled = $enabled;
    }

    /**
     * {@inheritdoc}
     */
    public function getEnabled()
    {
        return $this->enabled;
    }

    /**
     * {@inheritdoc}
     */
    public function setToken(string $token)
    {
        $this->token = $token;
    }

    /**
     * {@inheritdoc}
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * {@inheritdoc}
     */
    public function isEnabled()
    {
        return $this->enabled === true;
    }

    /**
     * {@inheritdoc}
     */
    public function setFeedIds(array $feedIds)
    {
        $this->feedIds = $feedIds;
    }

    /**
     * {@inheritdoc}
     */
    public function hasFeedIds()
    {
        return is_array($this->feedIds) && count($this->feedIds) > 0;
    }

    /**
     * {@inheritdoc}
     */
    public function getFeedIds()
    {
        return $this->feedIds;
    }

    /**
     * {@inheritdoc}
     */
    public function setConfiguration(ConnectorEngineConfigurationInterface $configuration)
    {
        $this->configuration = $configuration;
    }

    /**
     * {@inheritdoc}
     */
    public function getConfiguration()
    {
        return $this->configuration;
    }

}
