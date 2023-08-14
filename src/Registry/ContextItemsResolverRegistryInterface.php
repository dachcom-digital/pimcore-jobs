<?php

namespace JobsBundle\Registry;

use JobsBundle\Context\Resolver\ContextItemsResolverInterface;

interface ContextItemsResolverRegistryInterface
{
    public function has(string $identifier): bool;

    /**
     * @throws \Exception
     */
    public function get(string $identifier): ContextItemsResolverInterface;

    /**
     * @return array<int, ContextItemsResolverInterface>
     */
    public function getAll(): array;
}
