<?php

namespace JobsBundle\Schema\Generator;

use Spatie\SchemaOrg\Graph;
use Spatie\SchemaOrg\BaseType;
use Symfony\Component\HttpFoundation\Request;
use SchemaBundle\Generator\GeneratorInterface;
use JobsBundle\Connector\ConnectorServiceInterface;
use JobsBundle\Context\ContextServiceInterface;

class GoogleForJobsGraphGenerator implements GeneratorInterface
{
    /**
     * @var ContextServiceInterface
     */
    protected $contextService;

    /**
     * @var ConnectorServiceInterface
     */
    protected $connectorService;

    /**
     * @param ContextServiceInterface   $contextService
     * @param ConnectorServiceInterface $connectorService
     */
    public function __construct(
        ContextServiceInterface $contextService,
        ConnectorServiceInterface $connectorService
    ) {
        $this->contextService = $contextService;
        $this->connectorService = $connectorService;
    }

    /**
     * {@inheritDoc}
     */
    public function supportsRequest(Request $request, string $route): bool
    {
        if (!$this->connectorService->connectorDefinitionIsEnabled('google')) {
            return false;
        }

        $connectorDefinition = $this->connectorService->getConnectorDefinition('google', true);

        if ($connectorDefinition->isOnline() === false) {
            return false;
        }

        $resolvedItems = $this->contextService->resolveContextItems(
            'request',
            $connectorDefinition,
            [
                'request'            => $request,
                'is_preflight_check' => true
            ]
        );

        return count($resolvedItems) === 1;
    }

    /**
     * {@inheritDoc}
     */
    public function supportsElement($element): bool
    {
        return false;
    }

    /**
     * {@inheritDoc}
     */
    public function generateForRequest(Graph $graph, Request $request, array &$schemaBlocks): void
    {
        $connectorDefinition = $this->connectorService->getConnectorDefinition('google', true);

        $resolvedItems = $this->contextService->resolveContextItems(
            'request',
            $connectorDefinition,
            [
                'request' => $request
            ]
        );

        $this->connectorService->generateConnectorFeed('google', 'void', $resolvedItems, ['graph' => $graph]);
    }

    /**
     * {@inheritDoc}
     */
    public function generateForElement($element): ?BaseType
    {
        return null;
    }
}