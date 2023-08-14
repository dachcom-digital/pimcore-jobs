<?php

namespace JobsBundle\Repository;

use JobsBundle\Model\LogEntryInterface;
use Doctrine\ORM\Tools\Pagination\Paginator;

interface LogRepositoryInterface
{
    public function findForObject(int $objectId): Paginator;

    public function findForConnectorEngine(int $connectorEngineId): Paginator;

    public function findForConnectorEngineAndObject(int $connectorEngineId, int $objectId): Paginator;

    public function deleteForConnectorEngineAndObject(int $connectorEngineId, int $objectId): void;

    public function deleteForObject(int $objectId): void;

    public function deleteExpired(int $expireDays): void;

    /**
     * @throws \Exception
     */
    public function truncateLogTable(): void;
}
