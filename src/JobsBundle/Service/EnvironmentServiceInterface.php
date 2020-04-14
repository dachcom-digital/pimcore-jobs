<?php

namespace JobsBundle\Service;

interface EnvironmentServiceInterface
{
    /**
     * @return string
     */
    public function getDataClass();

    /**
     * @return string
     */
    public function getFeedHost();
}