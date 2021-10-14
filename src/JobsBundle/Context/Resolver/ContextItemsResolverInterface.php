<?php

namespace JobsBundle\Context\Resolver;

use JobsBundle\Connector\ConnectorDefinitionInterface;
use JobsBundle\Context\ResolvedItem;
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
     * @return array<int, ResolvedItem>
     */
    public function resolve(ConnectorDefinitionInterface $connectorDefinition, array $contextParameter): array;
}
