<?php

namespace JobsBundle\Registry;

use JobsBundle\Connector\ConnectorDefinitionInterface;

interface ConnectorDefinitionRegistryInterface
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
     * @return ConnectorDefinitionInterface
     *
     * @throws \Exception
     */
    public function get($identifier);

    /**
     * @return ConnectorDefinitionInterface[]
     */
    public function getAll();
}
