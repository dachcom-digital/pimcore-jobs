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

namespace JobsBundle\Repository;

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
