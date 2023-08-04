<?php

namespace JobsBundle\Service;

class EnvironmentService implements EnvironmentServiceInterface
{

    public function __construct(
        protected string $dataClass,
        protected string $feedHost
    ) {
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
