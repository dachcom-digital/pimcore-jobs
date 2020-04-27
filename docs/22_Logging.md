# Logging

![image](https://user-images.githubusercontent.com/700119/80360297-6aaa6f00-887f-11ea-9ff9-40326eb533b4.png)

Jobs Bundle comes with a dedicated log table.

## Clean Up Task
Logs will be removed after `30 days`. Change the expiration via configuration:
 
```yaml
jobs:
    log_expiration_days: 10
```

## Custom Clean-Up

### Object
Every Log-panel comes with a "Cleanup" button.
It will remove logs bounded to given connector **and** object only!
  
### Global Flush
There is also a global log flush workflow. Go to the global jobs config panel and hit the "Flush all logs"
button at the left top corner.

> Be aware this will truncate the logs tables and can't be undone!

## Add Log

```php
$log = $this->logManager->createNewForConnector('your_connnector');

$log->setType('success');
$log->setMessage('My Message');
$log->setObjectId(667);

$this->logManager->update($log);
```