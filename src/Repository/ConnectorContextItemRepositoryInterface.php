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

use JobsBundle\Model\ConnectorContextItemInterface;

interface ConnectorContextItemRepositoryInterface
{
    public function findById(int $id): ?ConnectorContextItemInterface;

    /**
     * @return array<int, ConnectorContextItemInterface>
     */
    public function findForObject(int $objectId): array;

    /**
     * @return array<int, ConnectorContextItemInterface>
     */
    public function findForConnectorEngine(int $connectorEngineId);

    /**
     * @return array<int, ConnectorContextItemInterface>
     */
    public function findForConnectorEngineAndObject(int $connectorEngineId, int $objectId);
}
