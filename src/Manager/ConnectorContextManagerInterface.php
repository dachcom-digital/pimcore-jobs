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

use JobsBundle\Model\ConnectorContextItemInterface;
use JobsBundle\Model\ContextDefinitionInterface;

interface ConnectorContextManagerInterface
{
    /**
     * @return array<int, ConnectorContextItemInterface>
     */
    public function getForObject(int $objectId): array;

    /**
     * @return array<int, ConnectorContextItemInterface>
     */
    public function getForConnectorEngine(int $connectorEngineId): array;

    /**
     * @return array<int, ConnectorContextItemInterface>
     */
    public function getForConnectorEngineAndObject(int $connectorEngineId, int $objectId): array;

    public function getContextDefinition(int $definitionContextId): ContextDefinitionInterface;

    public function connectorAllowsMultipleContextItems(string $connectorDefinitionName): bool;

    public function createNew(int $connectorId): ConnectorContextItemInterface;

    public function update(ConnectorContextItemInterface $connectorContextItem): ConnectorContextItemInterface;

    public function delete(ConnectorContextItemInterface $connectorContextItem): void;

    public function generateConnectorContextConfig(array $connectorContextItems): array;
}
