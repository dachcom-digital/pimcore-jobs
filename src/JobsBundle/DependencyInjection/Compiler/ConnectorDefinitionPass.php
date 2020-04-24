<?php

namespace JobsBundle\DependencyInjection\Compiler;

use JobsBundle\Registry\ConnectorDefinitionRegistry;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

final class ConnectorDefinitionPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $definition = $container->getDefinition(ConnectorDefinitionRegistry::class);
        foreach ($container->findTaggedServiceIds('jobs.connector_definition', true) as $id => $tags) {
            $connectorDefinition = $container->getDefinition($id);
            foreach ($tags as $attributes) {
                if ($container->hasParameter(sprintf('jobs.connectors.item_transformer.%s', $attributes['identifier']))) {
                    $connectorItemTransformerDefinition = $container->getParameter(sprintf('jobs.connectors.item_transformer.%s', $attributes['identifier']));
                    $connectorDefinition->addMethodCall('setItemTransformer', [$container->getDefinition($connectorItemTransformerDefinition)]);
                }

                $connectorDefinitionConfiguration = $container->getParameter(sprintf('jobs.connectors.system_config.%s', $attributes['identifier']));
                $connectorDefinition->addMethodCall('setDefinitionConfiguration', [$connectorDefinitionConfiguration]);
                $definition->addMethodCall('register', [new Reference($id), $attributes['identifier']]);
            }
        }
    }
}
