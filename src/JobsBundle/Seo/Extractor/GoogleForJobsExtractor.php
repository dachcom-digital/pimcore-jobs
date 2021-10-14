<?php

namespace JobsBundle\Seo\Extractor;

use JobsBundle\Service\EnvironmentService;
use JobsBundle\Service\EnvironmentServiceInterface;
use SeoBundle\MetaData\Extractor\ExtractorInterface;
use SeoBundle\Model\SeoMetaDataInterface;
use JobsBundle\Connector\ConnectorServiceInterface;
use JobsBundle\Context\ContextServiceInterface;
use Spatie\SchemaOrg\Graph;

class GoogleForJobsExtractor implements ExtractorInterface
{
    protected EnvironmentService $environmentService;
    protected ContextServiceInterface $contextService;
    protected ConnectorServiceInterface $connectorService;

    public function __construct(
        EnvironmentServiceInterface $environmentService,
        ContextServiceInterface $contextService,
        ConnectorServiceInterface $connectorService
    ) {
        $this->environmentService = $environmentService;
        $this->contextService = $contextService;
        $this->connectorService = $connectorService;
    }

    public function supports(mixed $element): bool
    {
        if (!$this->connectorService->connectorDefinitionIsEnabled('google')) {
            return false;
        }

        $connectorDefinition = $this->connectorService->getConnectorDefinition('google', true);
        if ($connectorDefinition->isOnline() === false) {
            return false;
        }

        $classPath = sprintf('Pimcore\Model\DataObject\%s', $this->environmentService->getDataClass());

        return $element instanceof $classPath;
    }

    public function updateMetaData(mixed $element, ?string $locale, SeoMetaDataInterface $seoMetadata): void
    {
        $connectorDefinition = $this->connectorService->getConnectorDefinition('google', true);

        $resolvedItems = $this->contextService->resolveContextItems(
            'pimcore_object',
            $connectorDefinition,
            [
                'element' => $element,
                'locale'  => $locale,
            ]
        );

        $graphMiddleware = $seoMetadata->getMiddleware('schema_graph');
        $graphMiddleware->addTask(function (SeoMetaDataInterface $seoMetadata, Graph $graph) use ($resolvedItems) {
            $this->connectorService->generateConnectorFeed('google', 'void', $resolvedItems, ['graph' => $graph]);
        });
    }
}
