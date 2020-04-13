# Pimcore Jobs Bundle
This bundle is currently under heavy development and not ready for production!

#### Requirements
* Pimcore >= 6.0.0

## Installation

```json
"require" : {
    "dachcom-digital/jobs" : "~1.0.0",
}
```

## Include Routes

```yaml
# app/config/routing.yml
jobs:
    data_class: MyJobDataClass
    context_definitions:
        -   id: 1
            locale: 'de'
            host: 'https://www.solverat.com'
        -   id: 2
            locale: 'en'
            host: 'https://www.solverat.com'

    available_connectors:
        -   connector_name: facebook
            connector_item_transformer: AppBundle\Transformer\FacebookItemTransformer
            connector_items_resolver:
                -   type: feed
        -   connector_name: google
            connector_item_transformer: AppBundle\Transformer\GoogleItemTransformer
            connector_items_resolver:
                -   type: seo_queue
                -   type: request
                    config:
                        route_name: 'my_object_route'
                        route_request_identifier: 'object_id'
                        route_object_identifier: 'id'
                        must_match_request_locale: true
```

## Configuration 

```yaml
jobs:
    data_class: Pimcore\Model\DataObject\Job
    available_connectors:
        -   connector_name: facebook
        -   connector_name: google
```

## Copyright and license
Copyright: [DACHCOM.DIGITAL](http://dachcom-digital.ch)  
For licensing details please visit [LICENSE.md](LICENSE.md)  

## Upgrade Info
Before updating, please [check our upgrade notes!](UPGRADE.md)
