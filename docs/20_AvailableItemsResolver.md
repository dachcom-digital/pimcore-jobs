# Available Items Resolver
Items Resolver are required to determinate items in a unknown context.
After resolving them, they need to return an array with `ResolvedItem`.

## Dynamic Route Request Resolver

| Name | Description
|------|------------|
| `route_name` | Determinates, on which route the job object is available |
| `route_request_identifier` | Which request fragment owns the object identifier (For Example `/?id=123` would be `id` |
| `route_object_identifier` | Determinates, on which object attribute the request identifier should get applied on |
| `is_localized_field` | If true, the object fetcher will look up with `getByLocalizedfields`  |
| `must_match_request_locale` | If true, the current request locale must match with the context definition locale  |

### Usage
````yaml
jobs:
    available_connectors:
        -   connector_name: google
            connector_items_resolver:
                -   type: dynamic_route_request
                    config:
                        route_namee: 'my_object_route'
                        route_request_identifier: 'object_id'
                        route_object_identifier: 'id'
                        must_match_request_locale: true
````

## Pimcore Object Resolver
No Configuration available.

### Usage
````yaml
jobs:
    available_connectors:
        -   connector_name: google
            connector_items_resolver:
                -   type: pimcore_object
````

## Seo Queue Resolver
No Configuration available.

### Usage
````yaml
jobs:
    available_connectors:
        -   connector_name: google
            connector_items_resolver:
                -   type: seo_queue
````

## Feed Resolver
No Configuration available.

### Usage
````yaml
jobs:
    available_connectors:
        -   connector_name: facebook
            connector_items_resolver:
                -   type: feed
````

## Custom Items Resolver
If you're planing to create your own connector (or override an existing one), checkout this [guide](21_CustomItemsResolver.md).