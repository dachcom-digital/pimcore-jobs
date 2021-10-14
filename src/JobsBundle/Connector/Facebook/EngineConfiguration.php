<?php

namespace JobsBundle\Connector\Facebook;

use JobsBundle\Connector\ConnectorEngineConfigurationInterface;

class EngineConfiguration implements ConnectorEngineConfigurationInterface
{
    /**
     * @internal
     */
    protected ?string $accessToken = null;

    /**
     * @internal
     */
    protected int|string $accessTokenExpiresAt;

    /**
     * @internal
     */
    protected ?string $recruitingManagerId = null;

    protected string $appId;
    protected string $appSecret;
    protected string $publisherName;
    protected string $publisherUrl;
    protected string $photoUrl;
    protected string $dataPolicyUrl;

    /**
     * @internal
     */
    public function setAccessToken(string $token): void
    {
        $this->accessToken = $token;
    }

    /**
     * @internal
     */
    public function setAccessTokenExpiresAt(int|string $expiresAt): void
    {
        $this->accessTokenExpiresAt = $expiresAt;
    }

    /**
     * @internal
     */
    public function getAccessToken(): ?string
    {
        return $this->accessToken;
    }

    /**
     * @internal
     */
    public function getRecruitingManagerId(): ?string
    {
        return $this->recruitingManagerId;
    }

    /**
     * @internal
     */
    public function setRecruitingManagerId(string $recruitingManagerId): void
    {
        $this->recruitingManagerId = $recruitingManagerId;
    }

    public function setAppId(string $appId): void
    {
        $this->appId = $appId;
    }

    public function getAppId(): string
    {
        return $this->appId;
    }

    public function setAppSecret(string $appSecret): void
    {
        $this->appSecret = $appSecret;
    }

    public function getAppSecret(): string
    {
        return $this->appSecret;
    }

    public function getPublisherName(): string
    {
        return $this->publisherName;
    }

    public function setPublisherName(string $publisherName): void
    {
        $this->publisherName = $publisherName;
    }

    public function getPublisherUrl(): string
    {
        return $this->publisherUrl;
    }

    public function setPublisherUrl(string $publisherUrl): void
    {
        $this->publisherUrl = $publisherUrl;
    }

    public function getPhotoUrl(): string
    {
        return $this->photoUrl;
    }

    public function setPhotoUrl(string $photoUrl): void
    {
        $this->photoUrl = $photoUrl;
    }

    public function getDataPolicyUrl(): string
    {
        return $this->dataPolicyUrl;
    }

    public function setDataPolicyUrl(string $dataPolicyUrl): void
    {
        $this->dataPolicyUrl = $dataPolicyUrl;
    }

    public function getConfigParam(string $param): mixed
    {
        $getter = sprintf('get%s', ucfirst($param));
        if (method_exists($this, $getter)) {
            return $this->$getter();
        }

        return null;
    }

    public function toBackendConfigArray() :array
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
