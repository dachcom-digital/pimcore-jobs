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

class ContextItemsResolverRegistry implements ContextItemsResolverRegistryInterface
{
    protected array $resolver = [];

    public function register(mixed $service, string $identifier): void
    {
        if (!in_array(ContextItemsResolverInterface::class, class_implements($service), true)) {
            throw new \InvalidArgumentException(
                sprintf('%s needs to implement "%s", "%s" given.', get_class($service), ContextItemsResolverInterface::class, implode(', ', class_implements($service)))
            );
        }

        $this->resolver[$identifier] = $service;
    }

    public function has($identifier): bool
    {
        return isset($this->resolver[$identifier]);
    }

    public function get($identifier): ContextItemsResolverInterface
    {
        if (!$this->has($identifier)) {
            throw new \Exception('Context Items Resolver "' . $identifier . '" does not exist');
        }

        return $this->resolver[$identifier];
    }

    public function getAll(): array
    {
        return $this->resolver;
    }
}
