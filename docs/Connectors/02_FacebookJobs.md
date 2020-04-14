# Facebook Jobs Connector

**Warning**: The Facebook Jobs XML Feed is still experimental! 

This Connector allows you to publish your Jobs on a xml output feed which will be parsed from the facebook company. 
To use this connector you need to provide fully configured Facebook App!
Before you start be sure you've checked out the [Setup Instructions](../00_Setup.md).

## Requirements
First things first. To use this connector, you have to install some dependencies:

- [facebook/graph-sdk](https://github.com/facebookarchive/php-graph-sdk/blob/5.x/README.md) (Mostly already installed within a Pimcore Installation)

## Example Configuration
This is a example Configuration.

Each Connector needs some `Items Resolver` (Find the right object for the right context) and a single `Item Transformer`
(Transform your object into a valid Facebook xml block in this case [full feed specification](https://developers.facebook.com/docs/pages/jobs-xml/getting-started#company-info)).

```yaml
jobs:
    data_class: Job
    available_connectors:
        -   connector_name: facebook
            connector_item_transformer: AppBundle\Transformer\FacebookItemTransformer
            connector_items_resolver:
                -   type: feed
```

## Connector Configuration
![image](https://user-images.githubusercontent.com/700119/79236809-bca9c880-7e6d-11ea-8d6f-11190a758ffb.png)

Now head back to the backend (`System` => `Jobs Configuration`) and checkout the facebook tab.
- Click on `Install`
- Click on `Enable`
- Before you hit the `Connect` button, you need to fill you out the Connector Configuration. After that, click "Save".
- Click `Connect`
  
## Connection
![image](https://user-images.githubusercontent.com/700119/79236998-f37fde80-7e6d-11ea-8b94-7bc015f50be0.png)

This will guide you through the facebook token generation. After hitting the "Connect" button, a new window will open. 
After a access token has been successfully generated, you can close the window. Click on "Check & Apply Connection" to finalize the connection state.

## Feed Generator
This Connector requires a fully registered feed. Click on "Add Feed" and start the generation process. First, it will generate a `Recruiting Manager Id` which is required by facebook.
If this step was successful, a second call will generate the feed itself. If this was successful too, your connector is fully configured and ready to use.

## Item Transformer
In our example we have a service class called `AppBundle\Transformer\FacebookItemTransformer`.
Every Item Transformer has its own logic as you can see here: 

```php
<?php

namespace AppBundle\Transformer;

use JobsBundle\Context\ResolvedItemInterface;
use JobsBundle\Transformer\ItemTransformerDefinitionInterface;
use JobsBundle\Transformer\ItemTransformerInterface;
use Pimcore\Model\DataObject\MyJobClass;

class FacebookItemTransformer implements ItemTransformerInterface
{
    /**
     * {@inheritDoc}
     */
    public function transform(ResolvedItemInterface $item, ItemTransformerDefinitionInterface $itemTransformerDefinition)
    {
        /** @var MyJobClass $subject */
        $subject = $item->getSubject();

        $contextDefinition = $item->getContextItem()->getContextDefinition();
        $locale = $contextDefinition->getLocale();

        $itemTransformerDefinition->setTitle($subject->getTitle());
        $itemTransformerDefinition->setSalary($subject->getSalary());
    }
}
```

## Item Resolver
There is only a simple items resolver required since the facebook connector will output its data via a [data feed](../11_Feeds.md).

## Done!
You're done. Go to your job object and enable all required [context definitions](../12_ObjectContext.md).