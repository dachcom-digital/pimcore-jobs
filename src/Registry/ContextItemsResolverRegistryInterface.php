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

namespace JobsBundle\Registry;

use JobsBundle\Context\Resolver\ContextItemsResolverInterface;

interface ContextItemsResolverRegistryInterface
{
    public function has(string $identifier): bool;

    /**
     * @throws \Exception
     */
    public function get(string $identifier): ContextItemsResolverInterface;

    /**
     * @return array<int, ContextItemsResolverInterface>
     */
    public function getAll(): array;
}
