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
     * {@inheritdoc}
     */
    public function getDataClass()
    {
        return $this->dataClass;
    }

    /**
     * {@inheritdoc}
     */
    public function getFeedHost()
    {
        return $this->feedHost;
    }
}
