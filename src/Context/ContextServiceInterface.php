<?php

namespace JobsBundle\Context;

use JobsBundle\Connector\ConnectorDefinitionInterface;

interface ContextServiceInterface
{
    /**
     * @return array<int, ResolvedItemInterface>
     * @throws \Exception
     */
    public function resolveContextItems(string $contextName, ConnectorDefinitionInterface $connectorDefinition, array $contextParameter): array;
}
