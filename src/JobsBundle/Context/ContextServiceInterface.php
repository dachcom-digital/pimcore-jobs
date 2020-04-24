<?php

namespace JobsBundle\Context;

use JobsBundle\Connector\ConnectorDefinitionInterface;

interface ContextServiceInterface
{
    /**
     * @param string                       $contextName
     * @param ConnectorDefinitionInterface $connectorDefinition
     * @param array                        $contextParameter
     *
     * @return array|ResolvedItem[]
     *
     * @throws \Exception
     */
    public function resolveContextItems(string $contextName, ConnectorDefinitionInterface $connectorDefinition, array $contextParameter);
}
