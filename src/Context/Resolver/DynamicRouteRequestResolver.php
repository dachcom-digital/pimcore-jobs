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

namespace JobsBundle\Context\Resolver;

use JobsBundle\Connector\ConnectorDefinitionInterface;
use JobsBundle\Context\ResolvedItem;
use JobsBundle\Manager\ConnectorContextManagerInterface;
use JobsBundle\Service\EnvironmentServiceInterface;
use Pimcore\Model\DataObject;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DynamicRouteRequestResolver implements ContextItemsResolverInterface
{
    protected array $configuration;
    protected EnvironmentServiceInterface $environmentService;
    protected ConnectorContextManagerInterface $connectorContextManager;

    public function __construct(ConnectorContextManagerInterface $connectorContextManager)
    {
        $this->connectorContextManager = $connectorContextManager;
    }

    public static function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'is_localized_field'        => false,
            'must_match_request_locale' => false,
            'route_name'                => null,
            'route_request_identifier'  => null,
            'route_object_identifier'   => null,
        ]);

        $resolver->setAllowedTypes('is_localized_field', ['bool']);
        $resolver->setAllowedTypes('must_match_request_locale', ['bool']);
        $resolver->setAllowedTypes('route_name', ['string', 'null']);
        $resolver->setAllowedTypes('route_request_identifier', ['string', 'null']);
        $resolver->setAllowedTypes('route_object_identifier', ['string', 'null']);
        $resolver->setRequired(['is_localized_field', 'must_match_request_locale', 'route_name', 'route_request_identifier', 'route_object_identifier']);
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
            'is_preflight_check' => false,
            'request'            => null
        ]);

        $resolver->setRequired(['request', 'is_preflight_check']);
        $resolver->setAllowedTypes('request', [Request::class]);
        $resolver->setAllowedTypes('is_preflight_check', ['bool']);
    }

    public function resolve(ConnectorDefinitionInterface $connectorDefinition, array $contextParameter): array
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

    protected function generateDummyForPreflight(Request $request): array
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
