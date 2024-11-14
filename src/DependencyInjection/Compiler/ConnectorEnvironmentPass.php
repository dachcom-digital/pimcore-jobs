<?php

namespace JobsBundle\DependencyInjection\Compiler;

use JobsBundle\Service\EnvironmentService;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class ConnectorEnvironmentPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $feedHost = $container->getParameter('jobs.feed_host');
        $dataClass = $container->getParameter('jobs.data_class');

        if (empty($feedHost)) {
            $pimcoreConfig = $container->getParameter('pimcore.config');
            $feedHost = $pimcoreConfig['general']['domain'];
            if (!str_contains($feedHost, 'http')) {
                $feedHost = sprintf('https://%s', $feedHost);
            }
        }

        $connectorServiceDefinition = $container->getDefinition(EnvironmentService::class);
        $connectorServiceDefinition->setArgument('$dataClass', $dataClass);
        $connectorServiceDefinition->setArgument('$feedHost', $feedHost);
    }
}
