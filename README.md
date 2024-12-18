# Pimcore Jobs Bundle
[![Software License](https://img.shields.io/badge/license-GPLv3-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Software License](https://img.shields.io/badge/license-DCL-white.svg?style=flat-square&color=%23ff5c5c)](LICENSE.md)
[![Latest Release](https://img.shields.io/packagist/v/dachcom-digital/jobs.svg?style=flat-square)](https://packagist.org/packages/dachcom-digital/jobs)
[![Tests](https://img.shields.io/github/actions/workflow/status/dachcom-digital/pimcore-jobs/.github/workflows/codeception.yml?branch=master&style=flat-square&logo=github&label=codeception)](https://github.com/dachcom-digital/pimcore-jobs/actions?query=workflow%3ACodeception+branch%3Amaster)
[![PhpStan](https://img.shields.io/github/actions/workflow/status/dachcom-digital/pimcore-jobs/.github/workflows/php-stan.yml?branch=master&style=flat-square&logo=github&label=phpstan%20level%204)](https://github.com/dachcom-digital/pimcore-jobs/actions?query=workflow%3A"PHP+Stan"+branch%3Amaster)

This Bundle allows you to synchronise your job offers with various connectors like [facebook jobs](https://developers.facebook.com/docs/pages/jobs-xml) or [google for jobs](https://developers.google.com/search/docs/data-types/job-posting).

![image](https://user-images.githubusercontent.com/700119/79226665-0a6b0480-7e5f-11ea-9774-810b076e7fcd.png)

### Release Plan

| Release | Supported Pimcore Versions | Supported Symfony Versions | Release Date | Maintained     | Branch     |
|---------|----------------------------|----------------------------|--------------|----------------|------------|
| **3.x** | `11.0`                     | `6.2`                      | 30.08.2023   | Feature Branch | dev-master |
| **2.x** | `10.5`, `10.6`             | `5.4`                      | 14.10.2021   | Unsupported    | 2.x        |
| **1.x** | `6.0` - `6.9`              | `3.4`, `^4.4`              | 27.04.2020   | Unsupported    | 1.x        |


## Installation

```json
"require" : {
    "dachcom-digital/jobs" : "~3.0.0",
}
```

Add Bundle to `bundles.php`:
```php
return [
    JobsBundle\JobsBundle::class => ['all' => true],
];
```

- Execute: `$ bin/console pimcore:bundle:install JobsBundle`

## Upgrading
- Execute: `$ bin/console doctrine:migrations:migrate --prefix 'JobsBundle\Migrations'`

## Usage
This Bundle needs some preparation. Please check out the [Setup](docs/00_Setup.md) guide first.

## Further Information
- [Setup](docs/00_Setup.md)
- [Connectors](./docs/10_Connectors.md)
  - [Google For Jobs](./docs/Connectors/01_GoogleForJobs.md)
  - [Facebook Jobs](./docs/Connectors/02_FacebookJobs.md)
- [Feeds](docs/11_Feeds.md)
- [Object Context](docs/12_ObjectContext.md)
- [Available Items Resolver](docs/20_AvailableItemsResolver.md)
  - [Custom Items Resolver](docs/21_CustomItemsResolver.md) (Expert)
- [Logging](docs/22_Logging.md)

## Upgrade Info
Before updating, please [check our upgrade notes!](UPGRADE.md)

## License
**DACHCOM.DIGITAL AG**, Löwenhofstrasse 15, 9424 Rheineck, Schweiz  
[dachcom.com](https://www.dachcom.com), dcdi@dachcom.ch  
Copyright © 2024 DACHCOM.DIGITAL. All rights reserved.  

For licensing details please visit [LICENSE.md](LICENSE.md)  
