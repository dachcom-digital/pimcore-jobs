<?php

namespace JobsBundle\Context\Resolver;

use JobsBundle\Service\EnvironmentServiceInterface;
use Pimcore\Model\DataObject;
use JobsBundle\Context\ResolvedItem;
use JobsBundle\Connector\ConnectorDefinitionInterface;
use JobsBundle\Manager\ConnectorContextManagerInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PimcoreObjectResolver implements ContextItemsResolverInterface
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
    public static function configureOptions(OptionsResolver $optionsResolver)
    {
        // no optinos
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
    public function setEnvironment(EnvironmentServiceInterface $environmentService)
    {
        $this->environmentService = $environmentService;
    }

    /**
     * {@inheritDoc}
     */
    public function configureContextParameter(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'element' => null,
            'locale'  => null,
            'host'    => null
        ]);

        $resolver->setRequired(['element', 'locale', 'host']);
        $resolver->setAllowedTypes('locale', 'string');
        $resolver->setAllowedTypes('locale', ['null', 'string']);
        $resolver->setAllowedTypes('element', DataObject\Concrete::class);
    }

    /**
     * {@inheritDoc}
     */
    public function resolve(ConnectorDefinitionInterface $connectorDefinition, array $contextParameter)
    {
        /** @var DataObject\Concrete $element */
        $element = $contextParameter['element'];

        $connectorContextItems = $this->connectorContextManager->getForConnectorEngineAndObject($connectorDefinition->getConnectorEngine()->getId(), $element->getId());

        $resolvedItems = [];
        foreach ($connectorContextItems as $contextItem) {
            $contextDefinition = $contextItem->getContextDefinition();
            if ($contextParameter['locale'] !== $contextDefinition->getLocale()) {
                continue;
            }

            if ($contextParameter['host'] !== null && $contextParameter['host'] !== $contextDefinition->getHost()) {
                continue;
            }

            $resolvedItems[] = new ResolvedItem($contextItem, $element, []);
        }

        return $resolvedItems;
    }
}