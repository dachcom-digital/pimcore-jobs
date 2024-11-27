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

namespace JobsBundle\Context;

use JobsBundle\Connector\ConnectorDefinitionInterface;
use JobsBundle\Registry\ContextItemsResolverRegistryInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContextService implements ContextServiceInterface
{
    public function __construct(protected ContextItemsResolverRegistryInterface $contextItemsResolverRegistry)
    {
    }

    public function resolveContextItems(string $contextName, ConnectorDefinitionInterface $connectorDefinition, array $contextParameter): array
    {
        if (!$this->contextItemsResolverRegistry->has($contextName)) {
            throw new \Exception(sprintf('Context Items Resolver "%s" not found.', $contextName));
        }

        try {
            $resolver = $this->contextItemsResolverRegistry->get($contextName);
        } catch (\Exception $e) {
            throw new \Exception(sprintf('Error while resolving context items with resolver "%s". %s.', $contextName, $e->getMessage()));
        }

        try {
            $optionsResolver = new OptionsResolver();
            $resolver->configureContextParameter($optionsResolver);
            $contextParameter = $optionsResolver->resolve($contextParameter);
        } catch (\Exception $e) {
            throw new \Exception(sprintf('Error while resolving context parameter for resolver "%s". %s.', $contextName, $e->getMessage()));
        }

        try {
            $items = $resolver->resolve($connectorDefinition, $contextParameter);
        } catch (\Exception $e) {
            throw new \Exception(sprintf('Error while resolving context items with resolver "%s". %s.', $contextName, $e->getMessage()));
        }

        return $items;
    }
}
