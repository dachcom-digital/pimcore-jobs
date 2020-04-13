<?php

namespace JobsBundle\Context\Resolver;

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
     * @var string
     */
    protected $dataClass;

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
    public function setDataClass(string $dataClass)
    {
        $this->dataClass = $dataClass;
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
            'feed' => null
        ]);

        $resolver->setRequired(['feed']);
        $resolver->setAllowedTypes('feed', ['null']);
    }

    /**
     * {@inheritDoc}
     */
    public function resolve(ConnectorDefinitionInterface $connectorDefinition, array $contextParameter)
    {
        $connectorContextItems = $this->connectorContextManager->getForConnectorEngine($connectorDefinition->getConnectorEngine()->getId());

        // @todo: Determinate feed!

        $resolvedItems = [];
        foreach ($connectorContextItems as $contextItem) {
            /** @var DataObject\Concrete $object */
            $object = DataObject::getById($contextItem->getObjectId());
            $resolvedItems[] = new ResolvedItem($contextItem, $object, []);
        }

        return $resolvedItems;
    }
}