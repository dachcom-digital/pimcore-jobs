<?php

/*
 * This source file is available under two different licenses:
 *   - GNU General Public License version 3 (GPLv3)
 *   - DACHCOM Commercial License (DCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) DACHCOM.DIGITAL AG (https://www.dachcom-digital.com)
 * @license    GPLv3 and DCL
 */

namespace JobsBundle\Manager;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Pagination\Paginator;
use JobsBundle\Model\LogEntry;
use JobsBundle\Model\LogEntryInterface;
use JobsBundle\Repository\LogRepositoryInterface;

class LogManager implements LogManagerInterface
{
    public function __construct(
        protected LogRepositoryInterface $logRepository,
        protected ConnectorManagerInterface $connectorManager,
        protected EntityManagerInterface $entityManager
    ) {
    }

    public function getForObject(int $objectId): Paginator
    {
        return $this->logRepository->findForObject($objectId);
    }

    public function getForConnectorEngine(int $connectorEngineId, int $offset, int $limit): Paginator
    {
        return $this->logRepository->findForConnectorEngine($connectorEngineId);
    }

    public function getForConnectorEngineAndObject(int $connectorEngineId, int $objectId, int $offset, int $limit): Paginator
    {
        return $this->logRepository->findForConnectorEngineAndObject($connectorEngineId, $objectId);
    }

    public function deleteForConnectorEngineAndObject(int $connectorEngineId, int $objectId): void
    {
        $this->logRepository->deleteForConnectorEngineAndObject($connectorEngineId, $objectId);
    }

    public function deleteForObject(int $objectId): void
    {
        $this->logRepository->deleteForObject($objectId);
    }

    public function flushLogs(): void
    {
        $this->logRepository->truncateLogTable();
    }

    public function createNew(): LogEntryInterface
    {
        $logEntry = new LogEntry();
        $logEntry->setCreationDate(new \DateTime());

        return $logEntry;
    }

    public function createNewForConnector(string $connectorName): LogEntryInterface
    {
        $connectorEngine = $this->connectorManager->getEngineByName($connectorName);

        $logEntry = new LogEntry();
        $logEntry->setConnectorEngine($connectorEngine);
        $logEntry->setCreationDate(new \DateTime());

        return $logEntry;
    }

    public function update(LogEntryInterface $logEntry): LogEntryInterface
    {
        $this->entityManager->persist($logEntry);
        $this->entityManager->flush();

        return $logEntry;
    }

    public function delete(LogEntryInterface $logEntry): void
    {
        $this->entityManager->remove($logEntry);
        $this->entityManager->flush();
    }
}
