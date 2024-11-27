<?php

namespace JobsBundle\Context\Resolver;

use JobsBundle\Connector\ConnectorDefinitionInterface;
use JobsBundle\Context\ResolvedItem;
use JobsBundle\Context\ResolvedItemInterface;
use JobsBundle\Manager\ConnectorContextManagerInterface;
use JobsBundle\Model\ConnectorContextItemInterface;
use JobsBundle\Service\EnvironmentServiceInterface;
use JobsBundle\Service\LinkGeneratorServiceInterface;
use Pimcore\Model\DataObject\Concrete;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SeoQueueResolver implements ContextItemsResolverInterface
{
    protected array $configuration;
    protected EnvironmentServiceInterface $environmentService;
    protected ConnectorContextManagerInterface $connectorContextManager;
    protected LinkGeneratorServiceInterface $linkGeneratorService;

    public function __construct(ConnectorContextManagerInterface $connectorContextManager, LinkGeneratorServiceInterface $linkGeneratorService)
    {
        $this->connectorContextManager = $connectorContextManager;
        $this->linkGeneratorService = $linkGeneratorService;
    }

    public static function configureOptions(OptionsResolver $resolver): void
    {
        // no options
    }

    public function setConfiguration(array $resolverConfiguration): void
    {
        $this->configuration = $resolverConfiguration;
    }

    public function setEnvironment(EnvironmentServiceInterface $environmentService): void
    {
        $this->environmentService = $environmentService;
    }

    public function configureContextParameter(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'resource' => null
        ]);

        $resolver->setRequired(['resource']);
        $resolver->setAllowedTypes('resource', [sprintf('Pimcore\Model\DataObject\%s', $this->environmentService->getDataClass())]);
    }

    public function resolve(ConnectorDefinitionInterface $connectorDefinition, array $contextParameter): array
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

    protected function generateQueueEntry(Concrete $object, ConnectorContextItemInterface $contextItem): ?ResolvedItemInterface
    {
        $id = $object->getId();
        $dataUrl = $this->linkGeneratorService->generate($object, $contextItem);

        if ($dataUrl === null) {
            return null;
        }

        if (!str_starts_with($dataUrl, 'http')) {
            return null;
        }

        return new ResolvedItem($contextItem, $object, [
            'type'    => sprintf('pimcore_%s', $object->getType()),
            'dataId'  => $id,
            'dataUrl' => $dataUrl
        ]);
    }
}
