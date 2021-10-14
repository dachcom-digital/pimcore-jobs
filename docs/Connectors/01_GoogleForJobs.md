# Google For Jobs Connector
This Connector allows you to push your Job Changes to the Google Index API in realtime also generating a schema block the 
detail page of your job object. Before you start be sure you've checked out the [Setup Instructions](../00_Setup.md).

## Requirements
First things first. To use this connector, you have to install some dependencies:

- [dachcom-digital/seo](https://github.com/dachcom-digital/pimcore-seo)
- [dachcom-digital/schema](https://github.com/dachcom-digital/pimcore-schema)

## Dependencies Configuration
Make sure the `pimcore_element_watcher.enabled: true` for the SeoBundle is enabled. 
Otherwise no object will be transmitted to the Google Index API after modification/deletion.

## Example Configuration
This is a example Configuration.

Each Connector needs some `Items Resolver` (Find the right object for the right context) and a single `Item Transformer`
(Transform your object into a valid schema block in this case).

```yaml
jobs:
    data_class: Job
    available_connectors:
        -   connector_name: google
            connector_item_transformer: App\Transformer\GoogleItemTransformer
            connector_items_resolver:
                -   type: seo_queue
                -   type: pimcore_object
```

## Item Resolver
The Google For Jobs Workflow is a tricky one. We need multiple resolver because there are two endpoints within its workflow.
The first one (`seo_queue`) needs to submit the object (Multiple times, based selected context definitions) to the google index api. 
The second one (`pimcore_object`) on the other hand only allows a single object - but may has to match with the given locale / host. 

Some items resolver does have some configurable options. Read more about all items resolver [here](../20_AvailableItemsResolver.md).

## Item Transformer
In our example we have a service class called `App\Transformer\GoogleItemTransformer`.
Every Item Transformer has its own logic as you can see here: 

```php
<?php

namespace App\Transformer;

use Carbon\Carbon;
use JobsBundle\Context\ResolvedItemInterface;
use JobsBundle\Transformer\ItemTransformerDefinitionInterface;
use JobsBundle\Transformer\ItemTransformerInterface;
use Pimcore\Model\DataObject\MyJobClass;
use Spatie\SchemaOrg\Graph;
use Spatie\SchemaOrg\Place;
use Spatie\SchemaOrg\Schema;

class GoogleItemTransformer implements ItemTransformerInterface
{
    public function transform(ResolvedItemInterface $item, ItemTransformerDefinitionInterface $itemTransformerDefinition): void
    {
        /** @var MyJobClass $subject */
        $subject = $item->getSubject();

        /** @var Graph $graph */
        $graph = $itemTransformerDefinition->getGraph();

        $contextDefinition = $item->getContextItem()->getContextDefinition();
        $locale = $contextDefinition->getLocale();

        $graph
            ->jobPosting()
            ->datePosted(Carbon::createFromTimestamp($subject->getCreationDate()))
            ->description($subject->getName())
            ->jobLocation($this->getJobLocation($subject))
            ->title($subject->getName())
            ->employmentType($subject->EmplymentType());

    }

    protected function getJobLocation(MyJobClass $job): Place
    {
        return Schema::place()
            ->name('Bregenz')
            ->address(Schema::postalAddress()
                ->addressCountry('Austria')
                ->addressRegion('Vorarlberg')
                ->addressLocality('Bregenz')
                ->postalCode('6900')
            );
    }
}
```

## Done!
You're done. Go to your job object and enable all required [context definitions](../12_ObjectContext.md).