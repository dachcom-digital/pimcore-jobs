<?php

namespace JobsBundle\Controller;

use JobsBundle\Connector\ConnectorServiceInterface;
use Pimcore\Controller\FrontendController;
use Symfony\Component\HttpFoundation\Request;

class ProviderController extends FrontendController
{
    /**
     * @var ConnectorServiceInterface
     */
    protected $connectorService;

    /**
     * @param ConnectorServiceInterface $connectorService
     */
    public function __construct(ConnectorServiceInterface $connectorService)
    {
        $this->connectorService = $connectorService;
    }

    /**
     * @param Request $request
     * @param string  $connectorName
     * @param string  $token
     */
    public function provideStreamAction(Request $request, string $connectorName, string $token)
    {
        if (!$this->connectorService->connectorIsEnabled($connectorName)) {
            throw $this->createNotFoundException('Not Found');
        }

        if ($token !== $this->connectorService->getConnectorToken($connectorName)) {
            throw $this->createNotFoundException('Not Found');
        }

        if (!$this->connectorService->connectorHasDataFeed($connectorName)) {
            throw $this->createNotFoundException('Not Found');
        }

        // show data stream
        throw new \Exception('This feature is not implemented.');
    }
}
