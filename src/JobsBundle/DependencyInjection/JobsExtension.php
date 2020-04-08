<?php

namespace JobsBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;

class JobsExtension extends Extension
{
    /**
     * @param array            $configs
     * @param ContainerBuilder $container
     *
     * @throws \Exception
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new YamlFileLoader($container, new FileLocator([__DIR__ . '/../Resources/config']));
        $loader->load('services.yml');

        $persistenceConfig = $config['persistence']['doctrine'];
        $entityManagerName = $persistenceConfig['entity_manager'];

        $container->setParameter('jobs.persistence.doctrine.enabled', true);
        $container->setParameter('jobs.persistence.doctrine.manager', $entityManagerName);

        $enabledConnectorNames = [];
        foreach ($config['enabled_connectors'] as $enabledConnector) {
            $enabledConnectorNames[] = $enabledConnector['connector_name'];
        }

        $container->setParameter('jobs.entity.data_class', is_null($config['data_class']) ? '' : $config['data_class']);
        $container->setParameter('jobs.connectors.enabled', $enabledConnectorNames);

        $this->checkDependencies($loader);
    }

    /**
     * @param YamlFileLoader $loader
     *
     * @throws \Exception
     */
    protected function checkDependencies(YamlFileLoader $loader)
    {
        if (!class_exists('JobsBundle\JobsBundle')) {
            return;
        }

        $loader->load('external/seo.yml');
    }
}
