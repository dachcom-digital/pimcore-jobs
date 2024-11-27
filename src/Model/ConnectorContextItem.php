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

class ConnectorContextItem implements ConnectorContextItemInterface
{
    protected ?int $id = null;
    protected ?int $objectId = null;
    protected ?ConnectorEngineInterface $connectorEngine;
    protected ?ContextDefinitionInterface $contextDefinition;

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setObjectId(?int $objectId): void
    {
        $this->objectId = $objectId;
    }

    public function getObjectId(): ?int
    {
        return $this->objectId;
    }

    public function setConnectorEngine(ConnectorEngineInterface $connectorEngine): void
    {
        $this->connectorEngine = $connectorEngine;
    }

    public function getConnectorEngine(): ?ConnectorEngineInterface
    {
        return $this->connectorEngine;
    }

    public function setContextDefinition(ContextDefinitionInterface $contextDefinition): void
    {
        $this->contextDefinition = $contextDefinition;
    }

    public function getContextDefinition(): ?ContextDefinitionInterface
    {
        return $this->contextDefinition;
    }

    public function __clone()
    {
        $this->id = null;
    }
}
