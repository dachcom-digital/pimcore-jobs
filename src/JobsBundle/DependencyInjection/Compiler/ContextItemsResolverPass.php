<?php

namespace JobsBundle\DependencyInjection\Compiler;

use JobsBundle\Context\Resolver\ContextItemsResolverInterface;
use JobsBundle\Registry\ContextItemsResolverRegistry;
use JobsBundle\Service\EnvironmentService;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class ContextItemsResolverPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $i = 0;
        $services = [];
        $definition = $container->getDefinition(ContextItemsResolverRegistry::class);

        foreach ($container->findTaggedServiceIds('jobs.context.items_resolver', true) as $serviceId => $attributes) {
            foreach ($attributes as $attribute) {
                if (isset($attribute['priority'])) {
                    $priority = $attribute['priority'];
                }

                $priority = $priority ?? 0;
                $services[] = [$priority, ++$i, $serviceId, $attribute];
            }
        }

        uasort($services, static function ($a, $b) {
            return $b[0] <=> $a[0] ?: $a[1] <=> $b[1];
        });

        foreach ($services as [, $index, $serviceId, $attributes]) {
            if (!isset($attributes['identifier'])) {
                throw new InvalidArgumentException(sprintf('Attribute "identifier" missing for context item resolver "%s".', $serviceId));
            }

            // If no configuration is available, there is also no need for this ItemsResolver.
            if (!$container->hasParameter(sprintf('jobs.connectors.items_resolver.%s', $attributes['identifier']))) {
                continue;
            }

            $itemsResolverDefinition = $container->getDefinition($serviceId);
            $itemsResolverConfig = $container->getParameter(sprintf('jobs.connectors.items_resolver.%s', $attributes['identifier']));

            $options = new OptionsResolver();
            /** @var ContextItemsResolverInterface $class */
            $class = $itemsResolverDefinition->getClass();
            $class::configureOptions($options);

            try {
                $resolvedOptions = $options->resolve($itemsResolverConfig);
            } catch (\Throwable $e) {
                throw new \Exception(sprintf('Invalid "%s" items resolver options. %s', $serviceId, $e->getMessage()));
            }

            $itemsResolverDefinition->addMethodCall('setConfiguration', [$resolvedOptions]);
            $itemsResolverDefinition->addMethodCall('setEnvironment', [$container->getDefinition(EnvironmentService::class)]);

            $definition->addMethodCall('register', [new Reference($serviceId), $attributes['identifier']]);
        }
    }
}
