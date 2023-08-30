<?php

namespace JobsBundle\Service;

use I18nBundle\Builder\RouteParameterBuilder;
use I18nBundle\LinkGenerator\I18nLinkGeneratorInterface;
use JobsBundle\Model\ConnectorContextItemInterface;
use Pimcore\Model\DataObject\ClassDefinition\LinkGeneratorInterface;
use Pimcore\Model\DataObject\Concrete;
use Pimcore\Model\Site;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class LinkGeneratorService implements LinkGeneratorServiceInterface
{
    public function __construct(protected UrlGeneratorInterface $urlGenerator)
    {
    }

    public function generate(Concrete $object, ConnectorContextItemInterface $contextItem): ?string
    {
        $linkGeneratorContext = [
            'contextName'          => 'jobs.link_generator',
            'connectorContextItem' => $contextItem
        ];

        $dataUrl = null;
        $linkGenerator = $object->getClass()->getLinkGenerator();

        // support for i18n
        if ($linkGenerator instanceof I18nLinkGeneratorInterface) {

            $definition = $contextItem->getContextDefinition();

            $context = [];
            $routeParameter = [
                '_locale' => $definition->getLocale(),
            ];

            $pimcoreSite = Site::getByDomain(parse_url($definition->getHost(), PHP_URL_HOST));

            if ($pimcoreSite instanceof Site) {
                $context['site'] = $pimcoreSite;
            }

            $routeItemParameters = RouteParameterBuilder::buildForEntity($object, $routeParameter, $context);

            return $this->urlGenerator->generate('', $routeItemParameters, UrlGeneratorInterface::ABSOLUTE_URL);
        }

        if ($linkGenerator instanceof LinkGeneratorInterface) {
            $dataUrl = $linkGenerator->generate($object, $linkGeneratorContext);
        }

        return $dataUrl;
    }
}
