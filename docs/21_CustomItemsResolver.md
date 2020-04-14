# Custom Items Resolver

> **Warning**: Creating a custom Item Resolver is only required if you're planning to create your custom connector.

```yaml
services:
    AppBundle\Context\Resolver\MyCustomResolverResolver:
        tags:
            - { name: jobs.context.items_resolver, identifier: my_custom_resolver }
```

### Service

```php
<?php

namespace AppBundle\Context\Resolver;

use Pimcore\Model\DataObject;
use JobsBundle\Context\ResolvedItem;
use JobsBundle\Connector\ConnectorDefinitionInterface;
use JobsBundle\Context\Resolver\ContextItemsResolverInterface;
use JobsBundle\Manager\ConnectorContextManagerInterface;
use JobsBundle\Service\EnvironmentServiceInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MyCustomResolverResolver implements ContextItemsResolverInterface
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
     * {@inheritDoc}
     */
    public function setEnvironment(EnvironmentServiceInterface $environmentService)
    {
        $this->environmentService = $environmentService;
    }

    /**
     * {@inheritDoc}
     */
    public function setConfiguration(array $resolverConfiguration)
    {
        $this->configuration = $resolverConfiguration;
    }

    /**
     * {@inheritDoc}
     */
    public function configureContextParameter(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'my_option' => null
        ]);

        $resolver->setRequired(['my_option']);
    }

    /**
     * {@inheritDoc}
     */
    public function resolve(ConnectorDefinitionInterface $connectorDefinition, array $contextParameter)
    {
        // all context items matching with "my_connector".
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

```
## Usage

```yaml
jobs:
    data_class: MyJobClass
    available_connectors:
        -   connector_name: my_connector
            connector_items_resolver:
                -   type: my_custom_resolver
                    config:
                        my_option: test
```