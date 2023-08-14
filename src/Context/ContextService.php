<?php

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
