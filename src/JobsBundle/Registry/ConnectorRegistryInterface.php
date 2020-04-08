<?php

namespace JobsBundle\Registry;

use JobsBundle\Connector\JobsConnectorInterface;

interface ConnectorRegistryInterface
{
    /**
     * @param string $identifier
     *
     * @return bool
     */
    public function has($identifier);

    /**
     * @param string $identifier
     *
     * @return JobsConnectorInterface
     * @throws \Exception
     */
    public function get($identifier);

    /**
     * @return JobsConnectorInterface[]
     */
    public function getAll();

}
