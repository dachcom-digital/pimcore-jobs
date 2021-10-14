<?php

namespace JobsBundle\Service;

interface EnvironmentServiceInterface
{
    public function getDataClass(): string;

    public function getFeedHost(): string;
}
