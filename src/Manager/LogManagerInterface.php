<?php

namespace JobsBundle\Manager;

use Doctrine\ORM\Tools\Pagination\Paginator;
use JobsBundle\Model\LogEntryInterface;

interface LogManagerInterface
{
    public function getForObject(int $objectId): Paginator;

    public function getForConnectorEngine(int $connectorEngineId, int $offset, int $limit): Paginator;

    public function getForConnectorEngineAndObject(int $connectorEngineId, int $objectId, int $offset, int $limit): Paginator;

    public function deleteForConnectorEngineAndObject(int $connectorEngineId, int $objectId): void;

    public function deleteForObject(int $objectId): void;

    /**
     * @throws \Exception
     */
    public function flushLogs(): void;

    public function createNew(): LogEntryInterface;

    public function createNewForConnector(string $connectorName): LogEntryInterface;

    public function update(LogEntryInterface $logEntry): LogEntryInterface;

    public function delete(LogEntryInterface $logEntry): void;
}