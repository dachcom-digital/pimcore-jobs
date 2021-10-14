<?php

namespace JobsBundle\Service;

class EnvironmentService implements EnvironmentServiceInterface
{
    protected string $dataClass;
    protected string $feedHost;

    public function __construct(string $dataClass, string $feedHost)
    {
        $this->dataClass = $dataClass;
        $this->feedHost = $feedHost;
    }

    public function getDataClass(): string
    {
        return $this->dataClass;
    }

    public function getFeedHost(): string
    {
        return $this->feedHost;
    }
}
