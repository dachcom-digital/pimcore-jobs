<?php

namespace JobsBundle\Repository;

use JobsBundle\Model\ConnectorEngineInterface;

interface ConnectorEngineRepositoryInterface
{
    public function findById(int $id): ?ConnectorEngineInterface;

    public function findByName(string $name): ?ConnectorEngineInterface;

    public function findIdByName(string $name): ?ConnectorEngineInterface;

    /**
     * @return array<int, ConnectorEngineInterface>
     */
    public function findAll(): array;
}
