<?php

namespace JobsBundle\Controller;

use JobsBundle\Connector\ConnectorServiceInterface;
use JobsBundle\Context\ContextServiceInterface;
use JobsBundle\Tool\FeedIdHelper;
use Pimcore\Controller\FrontendController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ProviderController extends FrontendController
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
     * @param Request $request
     * @param string  $connectorName
     * @param string  $token
     * @param int     $feedId
     *
     * @return Response
     * @throws \Exception
     */
    public function provideFeedAction(Request $request, string $connectorName, string $token, int $feedId)
    {
        if (!$this->connectorService->connectorDefinitionIsEnabled($connectorName)) {
            throw $this->createNotFoundException('Not Found');
        }

        $connectorDefinition = $this->connectorService->getConnectorDefinition($connectorName, true);

        if (!$connectorDefinition->isOnline()) {
            throw $this->createNotFoundException('Not Found');
        }

        if ($token !== $connectorDefinition->getConnectorEngine()->getToken()) {
            throw $this->createNotFoundException('Not Found');
        }

        $feedIdHelper = new FeedIdHelper($connectorDefinition->getConnectorEngine());
        $resolvedFeedId = $feedIdHelper->findFeedId($feedId);

        if (is_null($resolvedFeedId)) {
            throw $this->createNotFoundException('Not Found');
        }

        $params = [
            'internal_feed_id' => $resolvedFeedId['internalId'],
            'external_feed_id' => $resolvedFeedId['externalId']
        ];

        $resolvedItems = $this->contextService->resolveContextItems('feed', $connectorDefinition, $params);
        $response = $this->connectorService->generateConnectorFeed($connectorName, 'xml', $resolvedItems, []);

        return new Response($response, 200, ['Content-Type' => 'text/xml']);
    }
}
