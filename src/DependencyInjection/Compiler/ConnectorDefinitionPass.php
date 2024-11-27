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

namespace JobsBundle\DependencyInjection\Compiler;

use JobsBundle\Registry\ConnectorDefinitionRegistry;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

final class ConnectorDefinitionPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
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
