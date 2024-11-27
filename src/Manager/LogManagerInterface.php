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
