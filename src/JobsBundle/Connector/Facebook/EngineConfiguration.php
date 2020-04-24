<?php

namespace JobsBundle\Connector\Facebook;

use JobsBundle\Connector\ConnectorEngineConfigurationInterface;

class EngineConfiguration implements ConnectorEngineConfigurationInterface
{
    /**
     * @internal
     *l
     *
     * @var string
     */
    protected $accessToken;

    /**
     * @internal
     *
     * @var string
     */
    protected $accessTokenExpiresAt;

    /**
     * @internal
     *
     * @var string
     */
    protected $recruitingManagerId;

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
    protected $photoUrl;

    /**
     * @var string
     */
    protected $dataPolicyUrl;

    /**
     * @param string $token
     *
     * @internal
     */
    public function setAccessToken($token)
    {
        $this->accessToken = $token;
    }

    /**
     * @param string $expiresAt
     *
     * @internal
     */
    public function setAccessTokenExpiresAt($expiresAt)
    {
        $this->accessTokenExpiresAt = $expiresAt;
    }

    /**
     * @return string
     *
     * @internal
     */
    public function getAccessToken()
    {
        return $this->accessToken;
    }

    /**
     * @return string
     *
     * @internal
     */
    public function getRecruitingManagerId()
    {
        return $this->recruitingManagerId;
    }

    /**
     * @param string $recruitingManagerId
     *
     * @internal
     */
    public function setRecruitingManagerId(string $recruitingManagerId)
    {
        $this->recruitingManagerId = $recruitingManagerId;
    }

    /**
     * @param string $appId
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
     * @return string
     */
    public function getPhotoUrl()
    {
        return $this->photoUrl;
    }

    /**
     * @param string $photoUrl
     */
    public function setPhotoUrl(string $photoUrl)
    {
        $this->photoUrl = $photoUrl;
    }

    /**
     * @return string
     */
    public function getDataPolicyUrl()
    {
        return $this->dataPolicyUrl;
    }

    /**
     * @param string $dataPolicyUrl
     */
    public function setDataPolicyUrl(string $dataPolicyUrl)
    {
        $this->dataPolicyUrl = $dataPolicyUrl;
    }

    /**
     * {@inheritdoc}
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
     * {@inheritdoc}
     */
    public function toBackendConfigArray()
    {
        return [
            'appId'         => $this->getAppId(),
            'appSecret'     => $this->getAppSecret(),
            'publisherName' => $this->getPublisherName(),
            'publisherUrl'  => $this->getPublisherUrl(),
            'photoUrl'      => $this->getPhotoUrl(),
            'dataPolicyUrl' => $this->getDataPolicyUrl(),
        ];
    }
}
