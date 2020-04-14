<?php

namespace JobsBundle\Controller;

use Facebook\Facebook;
use Facebook\Exceptions\FacebookSDKException;
use Facebook\FacebookResponse;
use JobsBundle\Connector\ConnectorServiceInterface;
use JobsBundle\Connector\Facebook\EngineConfiguration;
use JobsBundle\Connector\Facebook\Session\FacebookDataHandler;
use JobsBundle\Model\ConnectorEngineInterface;
use JobsBundle\Tool\FeedIdHelper;
use JobsBundle\Service\EnvironmentServiceInterface;
use Pimcore\Controller\FrontendController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class FacebookController extends FrontendController
{
    /**
     * @var EnvironmentServiceInterface
     */
    protected $environmentService;

    /**
     * @var ConnectorServiceInterface
     */
    protected $connectorService;

    /**
     * @param EnvironmentServiceInterface $environmentService
     * @param ConnectorServiceInterface   $connectorService
     */
    public function __construct(
        EnvironmentServiceInterface $environmentService,
        ConnectorServiceInterface $connectorService
    ) {
        $this->environmentService = $environmentService;
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
        $connectorDefinition = $this->connectorService->getConnectorDefinition('facebook', true);

        if (!$connectorDefinition->engineIsLoaded()) {
            throw $this->createNotFoundException('Not Found');
        }

        if ($token !== $connectorDefinition->getConnectorEngine()->getToken()) {
            throw $this->createNotFoundException('Not Found');
        }

        $connectorEngineConfig = $connectorDefinition->getConnectorEngine()->getConfiguration();
        if (!$connectorEngineConfig instanceof EngineConfiguration) {
            throw new HttpException(400, 'Invalid facebook configuration. Please configure your connector "facebook" in backend first.');
        }

        $fb = $this->getFacebook($connectorEngineConfig, $request->getSession());
        $helper = $fb->getRedirectLoginHelper();

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
        $connectorDefinition = $this->connectorService->getConnectorDefinition('facebook', true);

        if (!$connectorDefinition->engineIsLoaded()) {
            throw $this->createNotFoundException('Not Found');
        }

        $connectorEngineConfig = $connectorDefinition->getConnectorEngine()->getConfiguration();
        if (!$connectorEngineConfig instanceof EngineConfiguration) {
            throw new HttpException(400, 'Invalid facebook configuration. Please configure your connector "facebook" in backend first.');
        }

        $fb = $this->getFacebook($connectorEngineConfig, $request->getSession());
        $helper = $fb->getRedirectLoginHelper();

        if (!$accessToken = $helper->getAccessToken()) {
            if ($helper->getError()) {
                throw new HttpException(400, $helper->getError());
            } else {
                throw new HttpException(400, $request->query->get('error_message', 'Unknown Error.'));
            }
        }

        try {
            $oAuth2Client = $fb->getOAuth2Client();
            $accessToken = $oAuth2Client->getLongLivedAccessToken($accessToken);
        } catch (FacebookSDKException $e) {
            throw new HttpException(400, $e->getMessage());
        }

        $connectorEngineConfig->setAccessToken($accessToken->getValue());
        $connectorEngineConfig->setAccessTokenExpiresAt($accessToken->getExpiresAt());
        $this->connectorService->updateConnectorEngineConfiguration('facebook', $connectorEngineConfig);

        $response = new Response();
        $response->setContent('Successfully connected. You can now close this window and return to backend to complete the configuration.');

        return $response;
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function adminGenerateFeedAction(Request $request)
    {
        $generateState = $request->request->get('state');

        $connectorDefinition = $this->connectorService->getConnectorDefinition('facebook', true);

        if (!$connectorDefinition->engineIsLoaded()) {
            return $this->json(['success' => false, 'message' => 'Connector is not loaded.']);
        }

        $connectorEngine = $connectorDefinition->getConnectorEngine();
        $connectorConfiguration = $connectorEngine->getConfiguration();

        if (empty($connectorEngine->getToken())) {
            return $this->json(['success' => false, 'message' => 'Connector has no valid token.']);
        }

        if (!$connectorConfiguration instanceof EngineConfiguration) {
            return $this->json(['success' => false, 'message' => 'Connector has no valid configuration.']);
        }

        if (empty($connectorConfiguration->getAccessToken())) {
            return $this->json(['success' => false, 'message' => 'Invalid facebook access token. Please configure your connector "facebook" in backend first.']);
        }

        if ($generateState === 'initial') {
            if (empty($connectorConfiguration->getRecruitingManagerId())) {
                $confirmText = 'No Recruiting Manger configured. Do you want to create one now?';
                return $this->json(['success' => true, 'dispatchType' => 'confirm', 'confirmText' => $confirmText, 'state' => 'createRecruitingManager']);
            } else {
                $confirmText = sprintf('Recruiting Manger with Id "%s" found. Do you want to request a new feed now?', $connectorConfiguration->getRecruitingManagerId());
                return $this->json(['success' => true, 'dispatchType' => 'confirm', 'confirmText' => $confirmText, 'state' => 'createFeed']);
            }
        } elseif ($generateState === 'createRecruitingManager') {
            $response = $this->registerRecruitingManager($request->getSession(), $connectorEngine);
            return $this->json($response);
        } elseif ($generateState === 'createFeed') {
            $response = $this->registerFeed($request->getSession(), $connectorEngine);
            return $this->json($response);
        }

        return $this->json([
            'success' => false,
            'message' => sprintf('Cannot proceed. Invalid State "%s"', $generateState)
        ]);
    }

    /**
     * @param SessionInterface         $session
     * @param ConnectorEngineInterface $connectorEngine
     *
     * @return array
     */
    protected function registerRecruitingManager(SessionInterface $session, ConnectorEngineInterface $connectorEngine)
    {
        $connectorEngineConfig = $connectorEngine->getConfiguration();
        if (!$connectorEngineConfig instanceof EngineConfiguration) {
            return ['success' => false, 'message' => 'Invalid facebook configuration. Please configure your connector "facebook" in backend first.'];
        }

        $params = [
            'name'            => $connectorEngineConfig->getPublisherName(),
            'website_url'     => $connectorEngineConfig->getPublisherUrl(),
            'photo_url'       => $connectorEngineConfig->getPhotoUrl(),
            'data_policy_url' => $connectorEngineConfig->getDataPolicyUrl(),
        ];

        try {
            $fb = $this->getFacebook($connectorEngineConfig, $session);
            $response = $fb->post('/me/recruiting_managers', $params, $connectorEngineConfig->getAccessToken());
        } catch (FacebookSDKException $e) {
            return ['success' => false, 'message' => sprintf('Request error: %s', $e->getMessage())];
        }

        if (!$response instanceof FacebookResponse) {
            return ['success' => false, 'message' => 'Invalid Response.'];
        }

        $data = $response->getDecodedBody();

        if (!isset($data['id'])) {
            return ['success' => false, 'message' => 'Missing recruiting manager id parameter in response.'];
        }

        $connectorEngineConfig->setRecruitingManagerId($data['id']);
        $this->connectorService->updateConnectorEngineConfiguration('facebook', $connectorEngineConfig);

        $confirmText = sprintf('Recruiting Manger with Id "%s" successfully registered. Do you want to request a new feed now?', $data['id']);

        return [
            'success'      => true,
            'state'        => 'createFeed',
            'dispatchType' => 'confirm',
            'confirmText'  => $confirmText
        ];
    }

    /**
     * @param SessionInterface         $session
     * @param ConnectorEngineInterface $connectorEngine
     *
     * @return array
     */
    protected function registerFeed(SessionInterface $session, ConnectorEngineInterface $connectorEngine)
    {
        $token = $connectorEngine->getToken();
        $feedIdHelper = new FeedIdHelper($connectorEngine);
        $connectorEngineConfig = $connectorEngine->getConfiguration();

        if (!$connectorEngineConfig instanceof EngineConfiguration) {
            return ['success' => false, 'message' => 'Invalid facebook configuration. Please configure your connector "facebook" in backend first.'];
        }

        $recruitingManagerId = $connectorEngineConfig->getRecruitingManagerId();
        $newFeedId = $feedIdHelper->generateFeedId();

        if (empty($recruitingManagerId)) {
            return ['success' => false, 'message' => 'Invalid facebook recruiting manager id.. Please configure your connector "facebook" in backend first.'];
        }

        $params = [
            'feed_url'          => sprintf('%s/jobs/facebook/%s/feed/%s', $this->environmentService->getFeedHost(), $token, $newFeedId),
            'syncing_frequency' => 'DAILY', // "NONE"||"HOURLY"||"SIX_HOURS"||"TWELVE_HOURS"||"DAILY"
        ];

        try {
            $fb = $this->getFacebook($connectorEngineConfig, $session);
            $response = $fb->post(sprintf('/%s/job_feeds', $connectorEngineConfig->getRecruitingManagerId()), $params, $connectorEngineConfig->getAccessToken());
        } catch (FacebookSDKException $e) {
            return ['success' => false, 'message' => sprintf('Request error: %s', $e->getMessage())];
        }

        if (!$response instanceof FacebookResponse) {
            return ['success' => false, 'message' => 'Invalid Response.'];
        }

        $data = $response->getDecodedBody();

        if (!isset($data['feed_id'])) {
            return ['success' => false, 'message' => 'Missing parameter "feed_id" in response.'];
        }

        try {
            $feedIdHelper->addFeedId($newFeedId, $data['feed_id']);
        } catch (\Exception $e) {
            return ['success' => false, 'message' => sprintf('Error while generating new feed Id. %s', $e->getMessage())];
        }

        $this->connectorService->updateConnectorFeedIds('facebook', $feedIdHelper->getAsArray());

        return [
            'success'      => true,
            'state'        => null,
            'dispatchType' => 'success',
            'confirmText'  => sprintf('Feed ID "%s" successfully registered.', $data['feed_id'])
        ];
    }

    /**
     * @param EngineConfiguration $configuration
     * @param SessionInterface    $session
     *
     * @return Facebook
     * @throws FacebookSDKException
     */
    protected function getFacebook(EngineConfiguration $configuration, SessionInterface $session)
    {
        $fb = new Facebook([
            'app_id'                  => $configuration->getAppId(),
            'app_secret'              => $configuration->getAppSecret(),
            'persistent_data_handler' => new FacebookDataHandler($session),
            'default_graph_version'   => 'v2.8'
        ]);

        return $fb;
    }

}
