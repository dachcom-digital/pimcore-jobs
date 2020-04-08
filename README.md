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

## Configuration 

```yaml
jobs:
    data_class: Pimcore\Model\DataObject\Job
    enabled_connectors:
        -   connector_name: facebook
        -   connector_name: google
```

## Copyright and license
Copyright: [DACHCOM.DIGITAL](http://dachcom-digital.ch)  
For licensing details please visit [LICENSE.md](LICENSE.md)  

## Upgrade Info
Before updating, please [check our upgrade notes!](UPGRADE.md)
