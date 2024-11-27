<?php

/*
 * This source file is available under two different licenses:
 *   - GNU General Public License version 3 (GPLv3)
 *   - DACHCOM Commercial License (DCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) DACHCOM.DIGITAL AG (https://www.dachcom-digital.com)
 * @license    GPLv3 and DCL
 */

namespace JobsBundle\Controller;

use JobsBundle\Connector\ConnectorServiceInterface;
use JobsBundle\Context\ContextServiceInterface;
use JobsBundle\Tool\FeedIdHelper;
use Pimcore\Controller\FrontendController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ProviderController extends FrontendController
{
    public function __construct(
        protected ContextServiceInterface $contextService,
        protected ConnectorServiceInterface $connectorService
    ) {
    }

    /**
     * @throws \Exception
     */
    public function provideFeedAction(Request $request, string $connectorName, string $token, int $feedId): Response
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
