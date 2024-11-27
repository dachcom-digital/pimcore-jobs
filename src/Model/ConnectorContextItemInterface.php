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

namespace JobsBundle\Model;

interface ConnectorContextItemInterface
{
    public function setId(?int $id): void;

    public function getId(): ?int;

    public function setObjectId(?int $objectId): void;

    public function getObjectId(): ?int;

    public function setConnectorEngine(ConnectorEngineInterface $connectorEngine): void;

    public function getConnectorEngine(): ?ConnectorEngineInterface;

    public function setContextDefinition(ContextDefinitionInterface $contextDefinition): void;

    public function getContextDefinition(): ?ContextDefinitionInterface;
}
