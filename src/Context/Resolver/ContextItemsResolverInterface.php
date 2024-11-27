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

namespace JobsBundle\Context\Resolver;

use JobsBundle\Connector\ConnectorDefinitionInterface;
use JobsBundle\Context\ResolvedItemInterface;
use JobsBundle\Service\EnvironmentServiceInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

interface ContextItemsResolverInterface
{
    public static function configureOptions(OptionsResolver $resolver): void;

    /**
     * @throws \Exception
     */
    public function setConfiguration(array $resolverConfiguration);

    public function setEnvironment(EnvironmentServiceInterface $environmentService): void;

    /**
     * @param OptionsResolver $resolver
     */
    public function configureContextParameter(OptionsResolver $resolver): void;

    /**
     * @return array<int, ResolvedItemInterface>
     */
    public function resolve(ConnectorDefinitionInterface $connectorDefinition, array $contextParameter): array;
}
