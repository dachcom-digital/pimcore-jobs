<?php

namespace JobsBundle\Connector\Facebook;

use JobsBundle\Connector\ConnectorEngineConfigurationInterface;

class EngineConfiguration implements ConnectorEngineConfigurationInterface
{
    /**
     * @var string
     */
    protected $appId;

    /**
     * @var string
     */
    protected $appSecret;

    /**
     * @var string
     */
    protected $publisherName;

    /**
     * @var string
     */
    protected $publisherUrl;

    /**
     * @var string
     */
    protected $accessToken;

    /**
     * @var string
     */
    protected $accessTokenExpiresAt;

    /**
     * @param $appId
     */
    public function setAppId($appId)
    {
        $this->appId = $appId;
    }

    /**
     * @return string
     */
    public function getAppId()
    {
        return $this->appId;
    }

    /**
     * @param string $appSecret
     */
    public function setAppSecret($appSecret)
    {
        $this->appSecret = $appSecret;
    }

    /**
     * @return string
     */
    public function getAppSecret()
    {
        return $this->appSecret;
    }

    /**
     * @param string $token
     */
    public function setAccessToken($token)
    {
        $this->accessToken = $token;
    }

    /**
     * @param string $expiresAt
     */
    public function setAccessTokenExpiresAt($expiresAt)
    {
        $this->accessTokenExpiresAt = $expiresAt;
    }

    /**
     * @return string
     */
    public function getAccessToken()
    {
        return $this->accessToken;
    }

    /**
     * @return string
     */
    public function getPublisherName()
    {
        return $this->publisherName;
    }

    /**
     * @param string $publisherName
     */
    public function setPublisherName(string $publisherName)
    {
        $this->publisherName = $publisherName;
    }

    /**
     * @return string
     */
    public function getPublisherUrl()
    {
        return $this->publisherUrl;
    }

    /**
     * @param string $publisherUrl
     */
    public function setPublisherUrl(string $publisherUrl)
    {
        $this->publisherUrl = $publisherUrl;
    }

    /**
     * {@inheritDoc}
     */
    public function getConfigParam(string $param)
    {
        $getter = sprintf('get%s', ucfirst($param));
        if (method_exists($this, $getter)) {
            return $this->$getter();
        }

        return null;
    }

    /**
     * {@inheritDoc}
     */
    public function toBackendConfigArray()
    {
        return [
            'appId'         => $this->getAppId(),
            'appSecret'     => $this->getAppSecret(),
            'publisherName' => $this->getPublisherName(),
            'publisherUrl'  => $this->getPublisherUrl(),
        ];
    }
}
