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
use Symfony\Component\OptionsResolver\OptionsResolver;

class PimcoreObjectResolver implements ContextItemsResolverInterface
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
        // no options
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
            'element' => null,
            'locale'  => null,
            'host'    => null
        ]);

        $resolver->setRequired(['element', 'locale', 'host']);
        $resolver->setAllowedTypes('locale', 'string');
        $resolver->setAllowedTypes('locale', ['null', 'string']);
        $resolver->setAllowedTypes('element', DataObject\Concrete::class);
    }

    public function resolve(ConnectorDefinitionInterface $connectorDefinition, array $contextParameter): array
    {
        /** @var DataObject\Concrete $element */
        $element = $contextParameter['element'];

        $connectorContextItems = $this->connectorContextManager->getForConnectorEngineAndObject($connectorDefinition->getConnectorEngine()->getId(), $element->getId());

        $resolvedItems = [];
        foreach ($connectorContextItems as $contextItem) {
            $contextDefinition = $contextItem->getContextDefinition();
            if ($contextParameter['locale'] !== $contextDefinition->getLocale()) {
                continue;
            }

            if ($contextParameter['host'] !== null && $contextParameter['host'] !== $contextDefinition->getHost()) {
                continue;
            }

            $resolvedItems[] = new ResolvedItem($contextItem, $element, []);
        }

        return $resolvedItems;
    }
}
