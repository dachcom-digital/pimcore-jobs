# Connectors

![image](https://user-images.githubusercontent.com/700119/79234884-3096a180-7e6b-11ea-8956-bf58969817c7.png)

Every connector has at least two stages: `Install` and `Enabled`.

## Installation
After pressing the Install button, the JobsBundle will generate a Database entry, a so called "Connector Engine".
It will provide a unique Token and optionally a configuration Class (Depends on Connector)

> **Warning:** If you want to uninstall a connector, all related data will be lost! 

## Enabling/Disabling
Enable or disable a connector. There is no data loss if a connector gets disabled.

## Connect
Not every Connector does have Connection Feature. 
The [Facebook Connector](./Connectors/02_FacebookJobs.md) for example, requires a valid access token which will be created, after you hit this button. 

***

## Setup Connector
Let's setup the [Google Connector](./Connectors/01_GoogleForJobs.md) to complete your configuration.

***

## Available Connector
- [Google For Jobs](./Connectors/01_GoogleForJobs.md)
- [Facebook Jobs](./Connectors/02_FacebookJobs.md)