<?php

namespace JobsBundle\Connector;

interface ConnectorEngineConfigurationInterface
{
    public function getConfigParam(string $param): mixed;

    public function toBackendConfigArray(): array;
}
