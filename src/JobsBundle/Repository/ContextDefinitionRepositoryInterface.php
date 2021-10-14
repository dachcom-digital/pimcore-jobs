<?php

namespace JobsBundle\Repository;

use JobsBundle\Model\ContextDefinitionInterface;

interface ContextDefinitionRepositoryInterface
{
    public function findById(int $id): ?ContextDefinitionInterface;

    public function findAll(): array;
}
