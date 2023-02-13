# Upgrade Notes

## 2.1.0
- [FEATURE] Pimcore 10.5 support only

## Migrating from Version 1.x to Version 2.0.0

### Global Changes
- PHP8 return type declarations added: you may have to adjust your extensions accordingly
- [FB Connector](./docs/Connectors/02_FacebookJobs.md): `facebookarchive/php-graph-sdk` has been removed, we're now using the `league/oauth2-facebook` package.

***

JobsBundle 1.x Upgrade Notes: https://github.com/dachcom-digital/pimcore-jobs/blob/1.x/UPGRADE.md
