<?php

namespace JobsBundle\Context\Resolver;

use JobsBundle\Connector\ConnectorDefinitionInterface;
use JobsBundle\Context\ResolvedItem;
use Symfony\Component\OptionsResolver\OptionsResolver;

interface ContextItemsResolverInterface
{
    /**
     * @param string $dataClass
     *
     * @return mixed
     */
    public function setDataClass(string $dataClass);

    /**
     * @param array $resolverConfiguration
     *
     * @throws \Exception
     */
    public function setConfiguration(array $resolverConfiguration);

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