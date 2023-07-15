<?php

namespace JobsBundle\DependencyInjection;

use JobsBundle\Service\EnvironmentService;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;

class JobsExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new YamlFileLoader($container, new FileLocator([__DIR__ . '/../Resources/config']));
        $loader->load('services.yaml');

        $persistenceConfig = $config['persistence']['doctrine'];
        $entityManagerName = $persistenceConfig['entity_manager'];

        $container->setParameter('jobs.persistence.doctrine.enabled', true);
        $container->setParameter('jobs.persistence.doctrine.manager', $entityManagerName);

        $availableConnectorsNames = [];
        foreach ($config['available_connectors'] as $availableConnector) {
            $availableConnectorsNames[] = $availableConnector['connector_name'];
            $itemTransformerParameter = sprintf('jobs.connectors.item_transformer.%s', $availableConnector['connector_name']);
            $container->setParameter($itemTransformerParameter, $availableConnector['connector_item_transformer']);
            foreach ($availableConnector['connector_items_resolver'] as $itemsResolverConfig) {
                $itemResolverParameter = sprintf('jobs.connectors.items_resolver.%s', $itemsResolverConfig['type']);
                $container->setParameter($itemResolverParameter, $itemsResolverConfig['config']);
            }
        }

        foreach (array_merge($this->getCoreConnectors(), $config['available_connectors']) as $availableConnector) {
            $container->setParameter(sprintf('jobs.connectors.system_config.%s', $availableConnector['connector_name']), $availableConnector['connector_config']);
        }

        $container->setParameter('jobs.connectors.available', $availableConnectorsNames);
        $container->setParameter('jobs.logs.expiration_days', $config['log_expiration_days']);

        $this->setupEnvironment($container, $config);
        $this->checkGoogleConnectorDependencies($container, $loader, $availableConnectorsNames);
    }

    protected function setupEnvironment(ContainerBuilder $container, array $config): void
    {
        $feedHost = is_string($config['feed_host']) ? $config['feed_host'] : '';
        $dataClass = is_string($config['data_class']) ? $config['data_class'] : '';

        if (empty($feedHost)) {
            $pimcoreConfig = $container->getParameter('pimcore.config');
            $feedHost = $pimcoreConfig['general']['domain'];
        }

        $connectorServiceDefinition = $container->getDefinition(EnvironmentService::class);
        $connectorServiceDefinition->setArgument('$dataClass', $dataClass);
        $connectorServiceDefinition->setArgument('$feedHost', $feedHost);
    }

    /**
     * @throws \Exception
     */
    protected function checkGoogleConnectorDependencies(ContainerBuilder $container, YamlFileLoader $loader, array $availableConnectorsNames): void
    {
        $container->setParameter('jobs.connector.google.dependencies_installed', false);

        if (!in_array('google', $availableConnectorsNames, true)) {
            return;
        }

        $bundles = $container->getParameter('kernel.bundles');
        /** @phpstan-ignore-next-line */
        if (array_key_exists('SeoBundle', $bundles) && array_key_exists('SchemaBundle', $bundles)) {
            $container->setParameter('jobs.connector.google.dependencies_installed', true);
            $loader->load('external/seo.yaml');
        }
    }

    protected function getCoreConnectors(): array
    {
        return [
            [
                'connector_name'   => 'google',
                'connector_config' => [
                    'core_disabled' => true
                ]
            ],
            [
                'connector_name'   => 'facebook',
                'connector_config' => [
                    'core_disabled' => true
                ]
            ]
        ];
    }
}
