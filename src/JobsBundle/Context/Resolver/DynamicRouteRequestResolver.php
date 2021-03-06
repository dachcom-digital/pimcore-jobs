<?php

namespace JobsBundle\Context\Resolver;

use JobsBundle\Connector\ConnectorDefinitionInterface;
use JobsBundle\Service\EnvironmentServiceInterface;
use Pimcore\Model\DataObject;
use JobsBundle\Context\ResolvedItem;
use JobsBundle\Manager\ConnectorContextManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DynamicRouteRequestResolver implements ContextItemsResolverInterface
{
    /**
     * @var array
     */
    protected $configuration;

    /**
     * @var EnvironmentServiceInterface
     */
    protected $environmentService;

    /**
     * @var ConnectorContextManagerInterface
     */
    protected $connectorContextManager;

    /**
     * @param ConnectorContextManagerInterface $connectorContextManager
     */
    public function __construct(ConnectorContextManagerInterface $connectorContextManager)
    {
        $this->connectorContextManager = $connectorContextManager;
    }

    /**
     * {@inheritdoc}
     */
    public static function configureOptions(OptionsResolver $optionsResolver)
    {
        $optionsResolver->setDefaults([
            'is_localized_field'        => false,
            'must_match_request_locale' => false,
            'route_name'                => null,
            'route_request_identifier'  => null,
            'route_object_identifier'   => null,
        ]);

        $optionsResolver->setAllowedTypes('is_localized_field', ['bool']);
        $optionsResolver->setAllowedTypes('must_match_request_locale', ['bool']);
        $optionsResolver->setAllowedTypes('route_name', ['string', 'null']);
        $optionsResolver->setAllowedTypes('route_request_identifier', ['string', 'null']);
        $optionsResolver->setAllowedTypes('route_object_identifier', ['string', 'null']);
        $optionsResolver->setRequired(['is_localized_field', 'must_match_request_locale', 'route_name', 'route_request_identifier', 'route_object_identifier']);
    }

    /**
     * {@inheritdoc}
     */
    public function setConfiguration(array $resolverConfiguration)
    {
        $this->configuration = $resolverConfiguration;
    }

    /**
     * {@inheritdoc}
     */
    public function setEnvironment(EnvironmentServiceInterface $environmentService)
    {
        $this->environmentService = $environmentService;
    }

    /**
     * {@inheritdoc}
     */
    public function configureContextParameter(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'is_preflight_check' => false,
            'request'            => null
        ]);

        $resolver->setRequired(['request', 'is_preflight_check']);
        $resolver->setAllowedTypes('request', [Request::class]);
        $resolver->setAllowedTypes('is_preflight_check', ['bool']);
    }

    /**
     * {@inheritdoc}
     */
    public function resolve(ConnectorDefinitionInterface $connectorDefinition, array $contextParameter)
    {
        /** @var Request $request */
        $request = $contextParameter['request'];
        $isPreFlightCheck = $contextParameter['is_preflight_check'] === true;
        $classPath = sprintf('Pimcore\Model\DataObject\%s', $this->environmentService->getDataClass());

        $detailRouteRequestIdentifier = $this->configuration['route_request_identifier'];
        $detailRouteObjectIdentifier = $this->configuration['route_object_identifier'];

        if ($isPreFlightCheck === true) {
            return $this->generateDummyForPreflight($request);
        }

        $requestIdentifierValue = $request->get($detailRouteRequestIdentifier, null);

        if ($requestIdentifierValue === null) {
            return [];
        }

        $object = null;
        if ($detailRouteObjectIdentifier === 'id' && method_exists($classPath, 'getById')) {
            $object = $classPath::getById($requestIdentifierValue);
        }

        if ($this->configuration['is_localized_field'] === true) {
            if (is_callable([$classPath, 'getByLocalizedfields'])) {
                return $classPath::getByLocalizedfields($detailRouteObjectIdentifier, $detailRouteObjectIdentifier, $request->getLocale(), ['limit' => 1]);
            }

            return [];
        }

        $getter = sprintf('getBy%s', ucfirst($detailRouteObjectIdentifier));
        if (is_callable([$classPath, $getter])) {
            $listing = $classPath::$getter($requestIdentifierValue);
            if ($listing instanceof DataObject\Listing) {
                $objects = $listing->getObjects();
                if (count($objects) === 1) {
                    $object = $objects[0];
                }
            }
        }

        if (!$object instanceof $classPath) {
            return [];
        }

        if (!$object instanceof DataObject\Concrete) {
            return [];
        }

        $connectorContextItems = $this->connectorContextManager->getForConnectorEngineAndObject($connectorDefinition->getConnectorEngine()->getId(), $object->getId());
        if (count($connectorContextItems) === 0) {
            return [];
        }

        $resolvedItems = [];
        foreach ($connectorContextItems as $contextItem) {
            if ($this->configuration['must_match_request_locale'] === true) {
                $contextDefinition = $contextItem->getContextDefinition();
                if ($request->getLocale() !== $contextDefinition->getLocale()) {
                    continue;
                }

                // host check?
            }

            $resolvedItems[] = new ResolvedItem($contextItem, $object, []);
        }

        return $resolvedItems;
    }

    /**
     * @param Request $request
     *
     * @return array
     */
    protected function generateDummyForPreflight(Request $request)
    {
        if ($request->get($this->configuration['route_request_identifier'], null) === null) {
            return [];
        }

        if ($request->get('_route') !== $this->configuration['route_name']) {
            return [];
        }

        return [new ResolvedItem(null, null)];
    }
}
