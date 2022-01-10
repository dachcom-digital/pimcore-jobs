# Setup

After you have enabled this Bundle, there are some global steps to define.

### I. Data Class
If you don't have any pimcore data object to manage your jobs you need to create it first.
After that, you need to tell JobsBundle about it:

```yaml
# config/packages/jobs.yaml
jobs:
    data_class: MyJobDataClass
```

### II. Data Class Context Field
![image](https://user-images.githubusercontent.com/700119/79228214-5dde5200-7e61-11ea-8771-16def34b5a1f.png)

Add the "Jobs Connector Context" field to your data class. It would be good practice if it's placed in a dedicated tab.
Name it (Field "name") `jobConnectorContext`.

You can check the Health state by visiting the Jobs Menu `"Settings"` => `"Jobs Configuration"`.
Watch out for this information:   

![image](https://user-images.githubusercontent.com/700119/79228442-b7df1780-7e61-11ea-8885-d11ff3bc3877.png)


### III. Define Feed Host
Some connectors require an interface from which they can fetch the data. For certain connectors, the path even has to be submitted first (Like Facebook).
This forces us to define a global feed host. 

```yaml
# config/packages/jobs.yaml
jobs:
    data_class: MyJobDataClass
    feed_host: 'http://www.my-company.com'
```

There can be only one host per instance. But no worries, you're still able to publish jobs for multisites by using the context definitions, which we gonna checkout next.

### IV. Context Definitions

![image](https://user-images.githubusercontent.com/700119/79229352-2a042c00-7e63-11ea-81f6-0e5add8606b7.png)

Context definitions determine which jobs should get published on which portal with a given `locale` and `host`.
Since an object can live in multiple ways, we need to make things certain.

For Example facebook does not have any possibilities to publish jobs in multiple locales. But within the google context it's possible for sure.

Click on the `Add` button to create your first Context Definition:

![image](https://user-images.githubusercontent.com/700119/79229692-c0385200-7e63-11ea-90d3-c156443a6f6a.png)

| Name | Description
|------|------------|
| `Host` | Define your host. This is required to generate absolute links |
| `Locale` | Set a locale. The Job Object should get transformed within this locale |


You can add as many Context Definitions as you want. However, please note that some Connectors do not allow multiple definitions (Like Facebook). 

### V. Link Generator
Your Job Object needs a valid Link Generator. 
If you already have created a Link Generator make sure that you're respecting the host value.

A Link Generator could look like this:

```php
<?php

namespace App;

use JobsBundle\Model\ConnectorContextItemInterface;
use Pimcore\Model\DataObject\ClassDefinition\LinkGeneratorInterface;
use Pimcore\Model\DataObject\Concrete;
use Pimcore\Model\Staticroute;

class ObjectLinkGenerator implements LinkGeneratorInterface
{
    public function generate(Concrete $object, array $params = []): string
    {
        $staticRoute = Staticroute::getByName('my_object_route');

        $connectorContextConfig = null;
        $connectorContextItem = isset($params['connectorContextItem']) ? $params['connectorContextItem'] : null;
        if ($connectorContextItem instanceof ConnectorContextItemInterface) {
            $connectorContextConfig = $connectorContextItem->getContextDefinition();
        }

        $masterRequest = \Pimcore::getContainer()->get('request_stack')->getMasterRequest();

        $baseLocale = isset($params['_locale']) ? $params['_locale'] : $masterRequest->getLocale();
        $baseHost = isset($params['host']) ? $params['host'] : null;

        $locale = $connectorContextConfig !== null ? $connectorContextConfig->getLocale() : $baseLocale;
        $host = $connectorContextConfig !== null ? $connectorContextConfig->getHost() : $baseHost;

        $path = $staticRoute->assemble(['object_id' => $object->getId(), '_locale' => $locale]);

        if ($host !== null) {
            return sprintf('%s%s', $host, $path);
        }

        return $path;
    }
}
```

### VI. The Connector Configuration
This is the final step: Setup your Connectors. Each connectors has its own configuration and strategies.
Let's checkout the [Connector](./10_Connectors.md) Guide to learn how to use and install them. 

***

## Available Connector
- [Google For Jobs](./Connectors/01_GoogleForJobs.md)
- [Facebook Jobs](./Connectors/02_FacebookJobs.md)