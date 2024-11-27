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

use JobsBundle\Model\ContextDefinitionInterface;

interface ContextDefinitionManagerInterface
{
    public function getById(int $contextDefinitionId): ?ContextDefinitionInterface;

    /**
     * @return array<int, ContextDefinitionInterface>
     */
    public function getAll(): array;

    public function createNew(string $host, string $locale): ContextDefinitionInterface;

    public function update(ContextDefinitionInterface $contextDefinition): ContextDefinitionInterface;

    public function delete(ContextDefinitionInterface $contextDefinition): void;
}
