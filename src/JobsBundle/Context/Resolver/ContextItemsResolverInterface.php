<?php

namespace JobsBundle\Context\Resolver;

use JobsBundle\Connector\ConnectorDefinitionInterface;
use JobsBundle\Context\ResolvedItem;
use JobsBundle\Service\EnvironmentServiceInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

interface ContextItemsResolverInterface
{
    /**
     * @param OptionsResolver $resolver
     */
    public static function configureOptions(OptionsResolver $resolver);

    /**
     * @param array $resolverConfiguration
     *
     * @throws \Exception
     */
    public function setConfiguration(array $resolverConfiguration);

    /**
     * @param EnvironmentServiceInterface $environmentService
     *
     * @return mixed
     */
    public function setEnvironment(EnvironmentServiceInterface $environmentService);

    /**
     * @param OptionsResolver $resolver
     */
    public function configureContextParameter(OptionsResolver $resolver);

    /**
     * @param ConnectorDefinitionInterface $connectorDefinition
     * @param array                        $contextParameter
     *
     * @return array|ResolvedItem[]
     */
    public function resolve(ConnectorDefinitionInterface $connectorDefinition, array $contextParameter);
}
