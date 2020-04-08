<?php

namespace JobsBundle\Connector\Facebook;

use JobsBundle\Connector\JobsConnectorConfigurationInterface;

class Configuration implements JobsConnectorConfigurationInterface
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
    protected $accessToken;

    /**
     * @var string
     */
    protected $accessTokenExpiresAt;

    public function setAppId($appId)
    {
        $this->appId = $appId;
    }

    public function getAppId()
    {
        return $this->appId;
    }

    public function setAppSecret($appSecret)
    {
        $this->appSecret = $appSecret;
    }

    public function getAppSecret()
    {
        return $this->appSecret;
    }

    public function setAccessToken($token)
    {
        $this->accessToken = $token;
    }

    public function setAccessTokenExpiresAt($expiresAt)
    {
        $this->accessTokenExpiresAt = $expiresAt;
    }

    public function getAccessToken()
    {
        return $this->accessToken;
    }

    /**
     * @return array
     */
    public function toBackendConfigArray()
    {
        return [
            'appId'     => $this->getAppId(),
            'appSecret' => $this->getAppSecret(),
        ];
    }
}
