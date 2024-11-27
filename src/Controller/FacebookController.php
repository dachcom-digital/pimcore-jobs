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

use GuzzleHttp\Client;
use JobsBundle\Connector\ConnectorServiceInterface;
use JobsBundle\Connector\Facebook\EngineConfiguration;
use JobsBundle\Model\ConnectorEngineInterface;
use JobsBundle\Service\EnvironmentServiceInterface;
use JobsBundle\Tool\FeedIdHelper;
use League\OAuth2\Client\Provider\Facebook;
use League\OAuth2\Client\Token\AccessToken;
use Pimcore\Controller\FrontendController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class FacebookController extends FrontendController
{
    public function __construct(
        protected EnvironmentServiceInterface $environmentService,
        protected ConnectorServiceInterface $connectorService
    ) {
    }

    public function connectAction(Request $request, string $token): RedirectResponse
    {
        $connectorEngine = $this->assertConnectorEngine($token);

        $connectorEngineConfig = $connectorEngine->getConfiguration();
        if (!$connectorEngineConfig instanceof EngineConfiguration) {
            throw new HttpException(400, 'Invalid facebook configuration. Please configure your connector "facebook" in backend first.');
        }

        $provider = $this->getFacebookProvider($connectorEngineConfig, $connectorEngine->getToken());

        $authUrl = $provider->getAuthorizationUrl([
            'scope' => ['pages_show_list'],
        ]);

        $request->getSession()->set('FBRLH_oauth2state', $provider->getState());

        return $this->redirect($authUrl);
    }

    /**
     * @throws \Exception
     */
    public function checkAction(Request $request, string $token): Response
    {
        $connectorEngine = $this->assertConnectorEngine($token);

        $connectorEngineConfig = $connectorEngine->getConfiguration();
        if (!$connectorEngineConfig instanceof EngineConfiguration) {
            throw new HttpException(400, 'Invalid facebook configuration. Please configure your connector "facebook" in backend first.');
        }

        if (!$request->query->has('state') || $request->query->get('state') !== $request->getSession()->get('FBRLH_oauth2state')) {
            throw new HttpException(400, 'Required param state missing from persistent data.');
        }

        $provider = $this->getFacebookProvider($connectorEngineConfig, $connectorEngine->getToken());

        $defaultToken = $provider->getAccessToken('authorization_code', [
            'code' => $request->query->get('code')
        ]);

        if (!$defaultToken instanceof AccessToken) {
            $message = 'Could not generate access token';
            if ($request->query->has('error_message')) {
                $message = $request->query->get('error_message');
            }

            throw new HttpException(400, $message);
        }

        try {
            $accessToken = $provider->getLongLivedAccessToken($defaultToken);
        } catch (\Throwable $e) {
            throw new HttpException(400, sprintf('Failed exchanging token: %s', $e->getMessage()));
        }

        $connectorEngineConfig->setAccessToken($accessToken->getToken());
        $connectorEngineConfig->setAccessTokenExpiresAt($accessToken->getExpires());
        $this->connectorService->updateConnectorEngineConfiguration('facebook', $connectorEngineConfig);

        $response = new Response();
        $response->setContent('Successfully connected. You can now close this window and return to backend to complete the configuration.');

        return $response;
    }

    public function adminGenerateFeedAction(Request $request): JsonResponse
    {
        $generateState = $request->request->get('state');

        $connectorDefinition = $this->connectorService->getConnectorDefinition('facebook', true);

        if (!$connectorDefinition->engineIsLoaded()) {
            return $this->json(['success' => false, 'message' => 'Connector is not loaded.']);
        }

        $connectorEngine = $connectorDefinition->getConnectorEngine();

        if (!$connectorEngine instanceof ConnectorEngineInterface) {
            return $this->json(['success' => false, 'message' => 'Connector is not loaded.']);
        }

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
            }

            $confirmText = sprintf('Recruiting Manger with Id "%s" found. Do you want to request a new feed now?', $connectorConfiguration->getRecruitingManagerId());

            return $this->json(['success' => true, 'dispatchType' => 'confirm', 'confirmText' => $confirmText, 'state' => 'createFeed']);
        }

        if ($generateState === 'createRecruitingManager') {
            $response = $this->registerRecruitingManager($connectorEngine);

            return $this->json($response);
        }

        if ($generateState === 'createFeed') {
            $response = $this->registerFeed($connectorEngine);

            return $this->json($response);
        }

        return $this->json([
            'success' => false,
            'message' => sprintf('Cannot proceed. Invalid State "%s"', $generateState)
        ]);
    }

    protected function assertConnectorEngine($token): ConnectorEngineInterface
    {
        $connectorDefinition = $this->connectorService->getConnectorDefinition('facebook', true);

        if (!$connectorDefinition->engineIsLoaded()) {
            throw $this->createNotFoundException('Not Found');
        }

        $connectorEngine = $connectorDefinition->getConnectorEngine();
        if (!$connectorEngine instanceof ConnectorEngineInterface) {
            throw $this->createNotFoundException('Not Found');
        }

        if ($token !== $connectorEngine->getToken()) {
            throw $this->createNotFoundException('Not Found');
        }

        return $connectorEngine;
    }

    protected function registerRecruitingManager(ConnectorEngineInterface $connectorEngine): array
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
            $response = $this->makeGraphCall('/me/recruiting_managers', $params, $connectorEngineConfig);
        } catch (\Throwable $e) {
            return ['success' => false, 'message' => sprintf('Request error: %s', $e->getMessage())];
        }

        if ($response === null) {
            return ['success' => false, 'message' => 'Invalid Response.'];
        }

        if (!isset($response['id'])) {
            return ['success' => false, 'message' => 'Missing recruiting manager id parameter in response.'];
        }

        $connectorEngineConfig->setRecruitingManagerId($response['id']);
        $this->connectorService->updateConnectorEngineConfiguration('facebook', $connectorEngineConfig);

        $confirmText = sprintf('Recruiting Manger with Id "%s" successfully registered. Do you want to request a new feed now?', $response['id']);

        return [
            'success'      => true,
            'state'        => 'createFeed',
            'dispatchType' => 'confirm',
            'confirmText'  => $confirmText
        ];
    }

    protected function registerFeed(ConnectorEngineInterface $connectorEngine): array
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
            $response = $this->makeGraphCall(sprintf('/%s/job_feeds', $connectorEngineConfig->getRecruitingManagerId()), $params, $connectorEngineConfig);
        } catch (\Throwable $e) {
            return ['success' => false, 'message' => sprintf('Request error: %s', $e->getMessage())];
        }

        if ($response === null) {
            return ['success' => false, 'message' => 'Invalid Response.'];
        }

        if (!isset($response['feed_id'])) {
            return ['success' => false, 'message' => 'Missing parameter "feed_id" in response.'];
        }

        try {
            $feedIdHelper->addFeedId($newFeedId, $response['feed_id']);
        } catch (\Exception $e) {
            return ['success' => false, 'message' => sprintf('Error while generating new feed Id. %s', $e->getMessage())];
        }

        $this->connectorService->updateConnectorFeedIds('facebook', $feedIdHelper->getAsArray());

        return [
            'success'      => true,
            'state'        => null,
            'dispatchType' => 'success',
            'confirmText'  => sprintf('Feed ID "%s" successfully registered.', $response['feed_id'])
        ];
    }

    protected function makeGraphCall(string $endpoint, array $params, EngineConfiguration $engineConfiguration): ?array
    {
        $client = new Client([
            'base_uri' => 'https://graph.facebook.com/v2.10'
        ]);

        $response = $client->post($endpoint, array_merge([
            'query'       => [
                'access_token'    => $engineConfiguration->getAccessToken(),
                'appsecret_proof' => hash_hmac('sha256', $engineConfiguration->getAccessToken(), $engineConfiguration->getAppSecret()),
            ],
            'form_params' => $params
        ], $params));

        return json_decode($response->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);
    }

    protected function getFacebookProvider(EngineConfiguration $configuration, string $token): Facebook
    {
        return new Facebook([
            'clientId'        => $configuration->getAppId(),
            'clientSecret'    => $configuration->getAppSecret(),
            'redirectUri'     => $this->generateRedirectUri($token),
            'graphApiVersion' => 'v2.10',
        ]);
    }

    protected function generateRedirectUri(string $token): string
    {
        return $this->generateUrl('jobs_facebook_connect_check', ['token' => $token], UrlGeneratorInterface::ABSOLUTE_URL);
    }
}
