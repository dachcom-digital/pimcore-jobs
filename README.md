# Pimcore Jobs Bundle
[![Software License](https://img.shields.io/badge/license-GPLv3-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Latest Release](https://img.shields.io/packagist/v/dachcom-digital/jobs.svg?style=flat-square)](https://packagist.org/packages/dachcom-digital/jobs)
[![Tests](https://img.shields.io/github/workflow/status/dachcom-digital/pimcore-jobs/Codeception?style=flat-square&logo=github&label=codeception)](https://github.com/dachcom-digital/pimcore-jobs/actions?query=workflow%3A%22Codeception%22)
[![PhpStan](https://img.shields.io/github/workflow/status/dachcom-digital/pimcore-jobs/PHP%20Stan?style=flat-square&logo=github&label=phpstan%20level%202)](https://github.com/dachcom-digital/pimcore-jobs/actions?query=workflow%3A%22PHP%20Stan%22)

This Bundle allows you to synchronise your job offers with various connectors like [facebook jobs](https://developers.facebook.com/docs/pages/jobs-xml) or [google for jobs](https://developers.google.com/search/docs/data-types/job-posting).

![image](https://user-images.githubusercontent.com/700119/79226665-0a6b0480-7e5f-11ea-9774-810b076e7fcd.png)

#### Requirements
* Pimcore >= 6.0
- Symfony >= 4.4

## Installation

```json
"require" : {
    "dachcom-digital/jobs" : "~1.0.0",
}
```

### Installation via Extension Manager
After you have installed the Jobs Bundle via composer, open pimcore backend and go to `Tools` => `Extension`:
- Click the green `+` Button in `Enable / Disable` row
- Click the green `+` Button in `Install/Uninstall` row

### Installation via CommandLine
After you have installed the Jobs Bundle via composer:
- Execute: `$ bin/console pimcore:bundle:enable JobsBundle`
- Execute: `$ bin/console pimcore:bundle:install JobsBundle`

## Upgrading

### Upgrading via Extension Manager
After you have updated the Jobs Bundle via composer, open pimcore backend and go to `Tools` => `Extension`:
- Click the green `+` Button in `Update` row

### Upgrading via CommandLine
After you have updated the Jobs Bundle via composer:
- Execute: `$ bin/console pimcore:bundle:update JobsBundle`

### Migrate via CommandLine
Does actually the same as the update command and preferred in CI-Workflow:
- Execute: `$ bin/console pimcore:migrations:migrate -b JobsBundle`

## Usage
This Bundle needs some preparation. Please checkout the [Setup](docs/00_Setup.md) guide first.

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

## Copyright and license
Copyright: [DACHCOM.DIGITAL](http://dachcom-digital.ch)  
For licensing details please visit [LICENSE.md](LICENSE.md)  

## Upgrade Info
Before updating, please [check our upgrade notes!](UPGRADE.md)
