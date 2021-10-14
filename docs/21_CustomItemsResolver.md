# Custom Items Resolver

> **Warning**: Creating a custom Item Resolver is only required if you're planning to create your custom connector.

```yaml
services:
    App\Context\Resolver\MyCustomResolverResolver:
        tags:
            - { name: jobs.context.items_resolver, identifier: my_custom_resolver }
```

### Service

```php
<?php

namespace App\Context\Resolver;

use Pimcore\Model\DataObject;
use JobsBundle\Context\ResolvedItem;
use JobsBundle\Connector\ConnectorDefinitionInterface;
use JobsBundle\Context\Resolver\ContextItemsResolverInterface;
use JobsBundle\Manager\ConnectorContextManagerInterface;
use JobsBundle\Service\EnvironmentServiceInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MyCustomResolverResolver implements ContextItemsResolverInterface
{
    protected array $configuration;
    protected EnvironmentServiceInterface $environmentService;
    protected ConnectorContextManagerInterface $connectorContextManager;

    public function __construct(ConnectorContextManagerInterface $connectorContextManager)
    {
        $this->connectorContextManager = $connectorContextManager;
    }

    public function setEnvironment(EnvironmentServiceInterface $environmentService): void
    {
        $this->environmentService = $environmentService;
    }

    public function setConfiguration(array $resolverConfiguration): void
    {
        $this->configuration = $resolverConfiguration;
    }

    public function configureContextParameter(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'my_option' => null
        ]);

        $resolver->setRequired(['my_option']);
    }

    public function resolve(ConnectorDefinitionInterface $connectorDefinition, array $contextParameter): array
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