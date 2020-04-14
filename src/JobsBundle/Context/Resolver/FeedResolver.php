<?php

namespace JobsBundle\Context\Resolver;

use JobsBundle\Service\EnvironmentServiceInterface;
use Pimcore\Model\DataObject;
use JobsBundle\Context\ResolvedItem;
use JobsBundle\Connector\ConnectorDefinitionInterface;
use JobsBundle\Manager\ConnectorContextManagerInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FeedResolver implements ContextItemsResolverInterface
{
    /**
     * @var array
     */
    protected $configuration;

    /**
     * @var EnvironmentServiceInterface
     */
    protected $environmentService;

    /**
     * @var ConnectorContextManagerInterface
     */
    protected $connectorContextManager;

    /**
     * @param ConnectorContextManagerInterface $connectorContextManager
     */
    public function __construct(ConnectorContextManagerInterface $connectorContextManager)
    {
        $this->connectorContextManager = $connectorContextManager;
    }

    /**
     * {@inheritDoc}
     */
    public function setEnvironment(EnvironmentServiceInterface $environmentService)
    {
        $this->environmentService = $environmentService;
    }

    /**
     * {@inheritDoc}
     */
    public function setConfiguration(array $resolverConfiguration)
    {
        $this->configuration = $resolverConfiguration;
    }

    /**
     * {@inheritDoc}
     */
    public function configureContextParameter(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'internal_feed_id' => null,
            'external_feed_id' => null,
        ]);

        $resolver->setRequired(['internal_feed_id', 'external_feed_id']);
        $resolver->setAllowedTypes('internal_feed_id', ['null', 'int', 'string']);
        $resolver->setAllowedTypes('external_feed_id', ['null', 'int', 'string']);
    }

    /**
     * {@inheritDoc}
     */
    public function resolve(ConnectorDefinitionInterface $connectorDefinition, array $contextParameter)
    {
        // @todo: Determinate feed?
        $connectorContextItems = $this->connectorContextManager->getForConnectorEngine($connectorDefinition->getConnectorEngine()->getId());

        $resolvedItems = [];
        foreach ($connectorContextItems as $contextItem) {
            /** @var DataObject\Concrete $object */
            $object = DataObject::getById($contextItem->getObjectId());
            $resolvedItems[] = new ResolvedItem($contextItem, $object, []);
        }

        return $resolvedItems;
    }
}