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

class FeedResolver implements ContextItemsResolverInterface
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
            'internal_feed_id' => null,
            'external_feed_id' => null,
        ]);

        $resolver->setRequired(['internal_feed_id', 'external_feed_id']);
        $resolver->setAllowedTypes('internal_feed_id', ['null', 'int', 'string']);
        $resolver->setAllowedTypes('external_feed_id', ['null', 'int', 'string']);
    }

    public function resolve(ConnectorDefinitionInterface $connectorDefinition, array $contextParameter): array
    {
        // @todo: Determinate feed?
        $connectorContextItems = $this->connectorContextManager->getForConnectorEngine($connectorDefinition->getConnectorEngine()->getId());

        $resolvedItems = [];
        foreach ($connectorContextItems as $contextItem) {
            /** @var DataObject\Concrete $object */
            $object = DataObject::getById($contextItem->getObjectId());
            $resolvedItems[] = new ResolvedItem($contextItem, $object, []);
        }

        return $resolvedItems;
    }
}
