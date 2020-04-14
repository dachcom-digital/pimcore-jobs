<?php

namespace JobsBundle\Service;

class EnvironmentService implements EnvironmentServiceInterface
{
    /**
     * @var string
     */
    protected $dataClass;

    /**
     * @var string
     */
    protected $feedHost;

    /**
     * @param string $dataClass
     * @param string $feedHost
     */
    public function __construct(string $dataClass, string $feedHost)
    {
        $this->dataClass = $dataClass;
        $this->feedHost = $feedHost;
    }

    /**
     * {@inheritDoc}
     */
    public function getDataClass()
    {
        return $this->dataClass;
    }

    /**
     * {@inheritDoc}
     */
    public function getFeedHost()
    {
        return $this->feedHost;
    }

}