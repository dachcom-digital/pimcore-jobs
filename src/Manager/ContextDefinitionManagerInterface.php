<?php

namespace JobsBundle\Manager;

use JobsBundle\Model\ContextDefinitionInterface;

interface ContextDefinitionManagerInterface
{
    public function getById(int $contextDefinitionId): ?ContextDefinitionInterface;

    /**
     * @return array<int, ContextDefinitionInterface>
     */
    public function getAll(): array;

    public function createNew(string $host, string $locale): ContextDefinitionInterface;

    public function update(ContextDefinitionInterface $contextDefinition): ContextDefinitionInterface;

    public function delete(ContextDefinitionInterface $contextDefinition): void;
}
