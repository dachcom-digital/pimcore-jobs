<?php

namespace JobsBundle\Controller;

use Facebook\Facebook;
use Facebook\Exceptions\FacebookSDKException;
use JobsBundle\Connector\ConnectorServiceInterface;
use JobsBundle\Connector\Facebook\Configuration;
use Pimcore\Controller\FrontendController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class FacebookController extends FrontendController
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
     * @param string  $token
     *
     * @return RedirectResponse
     *
     * @throws FacebookSDKException
     */
    public function connectAction(Request $request, string $token)
    {
        if ($token !== $this->connectorService->getConnectorToken('facebook')) {
            throw $this->createNotFoundException('Not Found');
        }

        $connectorConfig = $this->connectorService->getConnectorConfiguration('facebook');
        if (!$connectorConfig instanceof Configuration) {
            throw new HttpException(400, 'Invalid facebook configuration. Please configure your connector "facebook" in backend first.');
        }

        $fb = $this->getFacebook($connectorConfig);
        $helper = $fb->getRedirectLoginHelper();

        $token = $this->connectorService->getConnectorToken('facebook');
        $callbackUrl = $this->generateUrl('jobs_facebook_connect_check', ['token' => $token], UrlGeneratorInterface::ABSOLUTE_URL);

        $permissions = ['pages_show_list'];
        $loginUrl = $helper->getLoginUrl($callbackUrl, $permissions);

        return $this->redirect($loginUrl);
    }

    /**
     * @param Request $request
     * @param string  $token
     *
     * @return Response
     *
     * @throws \Exception
     */
    public function checkAction(Request $request, string $token)
    {
        if ($token !== $this->connectorService->getConnectorToken('facebook')) {
            throw $this->createNotFoundException('Not Found');
        }

        $connectorConfig = $this->connectorService->getConnectorConfiguration('facebook');
        if (!$connectorConfig instanceof Configuration) {
            throw new HttpException(400, 'Invalid facebook configuration. Please configure your connector "facebook" in backend first.');
        }

        $fb = $this->getFacebook($connectorConfig);
        $helper = $fb->getRedirectLoginHelper();

        if (!$accessToken = $helper->getAccessToken()) {
            if ($helper->getError()) {
                throw new HttpException(400, $helper->getError());
            } else {
                throw new HttpException(400, $request->query->get('error_message', 'Unknown Error.'));
            }
        }

        $oAuth2Client = $fb->getOAuth2Client();

        try {
            $accessToken = $oAuth2Client->getLongLivedAccessToken($accessToken);
        } catch (FacebookSDKException $e) {
            throw new HttpException(400, $e->getMessage());
        }

        $connectorConfig->setAccessToken($accessToken->getValue());
        $connectorConfig->setAccessTokenExpiresAt($accessToken->getExpiresAt());
        $this->connectorService->updateConnectorConfiguration('facebook', $connectorConfig);

        $response = new Response();
        $response->setContent('Successfully connected. You can now close this window and return to backend to complete the configuration.');

        // @todo: register recruiting mangager
        // @todo: register feed!

        return $response;
    }

    /**
     * @param Configuration $configuration
     *
     * @return Facebook
     * @throws FacebookSDKException
     */
    protected function getFacebook(Configuration $configuration)
    {
        $fb = new Facebook([
            'app_id'                => $configuration->getAppId(),
            'app_secret'            => $configuration->getAppSecret(),
            'default_graph_version' => 'v2.8',
        ]);

        return $fb;
    }

}
