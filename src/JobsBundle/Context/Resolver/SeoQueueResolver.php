<?php

namespace JobsBundle\Context\Resolver;

use JobsBundle\Connector\ConnectorDefinitionInterface;
use JobsBundle\Context\ResolvedItem;
use JobsBundle\Context\ResolvedItemInterface;
use JobsBundle\Manager\ConnectorContextManagerInterface;
use JobsBundle\Model\ConnectorContextItemInterface;
use JobsBundle\Service\LinkGeneratorServiceInterface;
use Pimcore\Model\DataObject\Concrete;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SeoQueueResolver implements ContextItemsResolverInterface
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
     * @var LinkGeneratorServiceInterface
     */
    protected $linkGeneratorService;

    /**
     * @param ConnectorContextManagerInterface $connectorContextManager
     * @param LinkGeneratorServiceInterface    $linkGeneratorService
     */
    public function __construct(ConnectorContextManagerInterface $connectorContextManager, LinkGeneratorServiceInterface $linkGeneratorService)
    {
        $this->connectorContextManager = $connectorContextManager;
        $this->linkGeneratorService = $linkGeneratorService;
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
            'resource' => null
        ]);

        $resolver->setRequired(['resource']);
        $resolver->setAllowedTypes('resource', [sprintf('Pimcore\Model\DataObject\%s', $this->dataClass)]);
    }

    /**
     * {@inheritDoc}
     */
    public function resolve(ConnectorDefinitionInterface $connectorDefinition, array $contextParameter)
    {
        $resource = $contextParameter['resource'];
        if (!$resource instanceof Concrete) {
            return [];
        }

        $connectorContextItems = $this->connectorContextManager->getForConnectorEngineAndObject($connectorDefinition->getConnectorEngine()->getId(), $resource->getId());
        if (count($connectorContextItems) === 0) {
            return [];
        }

        $resolvedItems = [];
        foreach ($connectorContextItems as $contextItem) {

            $item = $this->generateQueueEntry($resource, $contextItem);
            if (!$item instanceof ResolvedItemInterface) {
                continue;
            }

            $resolvedItems[] = $item;
        }

        return $resolvedItems;
    }

    /**
     * @param Concrete                      $object
     * @param ConnectorContextItemInterface $contextItem
     *
     * @return ResolvedItemInterface|null
     */
    protected function generateQueueEntry(Concrete $object, ConnectorContextItemInterface $contextItem)
    {
        $type = 'unknown';
        $dataUrl = null;

        $id = $object->getId();
        $dataUrl = $this->linkGeneratorService->generate($object, $contextItem);

        if ($dataUrl === null) {
            return null;
        }

        if (substr($dataUrl, 0, 4) !== 'http') {
            return null;
        }

        if (method_exists($object, 'getType')) {
            $type = 'pimcore_' . $object->getType();
        }

        $resolvedItem = new ResolvedItem($contextItem, $object, [
            'type'    => $type,
            'dataId'  => $id,
            'dataUrl' => $dataUrl
        ]);

        return $resolvedItem;
    }
}