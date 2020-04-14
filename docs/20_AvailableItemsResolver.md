# Available Items Resolver
Items Resolver are required to determinate items in a unknown context.
After resolving them, they need to return an array with `ResolvedItem`.

## Request Resolver

| Name | Description
|------|------------|
| `route_name` | Determinates, on which route the job object is available |
| `route_request_identifier` | Which request fragment owns the object identifier (For Example `/?id=123` would be `id` |
| `route_object_identifier` | Determinates, on which object attribute the request identifier should get applied on |
| `is_localized_field` | If true, the object fetcher will look up with `getByLocalizedfields`  |
| `must_match_request_locale` | If true, the current request locale must match with the context definition locale  |

## Seo Queue Resolver
No Configuration available.

## Feed Resolver
No Configuration available.

## Custom Items Resolver
If you're planing to create your own connector (or override an existing one), checkout this [guide](21_CustomItemsResolver.md).