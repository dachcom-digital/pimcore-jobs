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
