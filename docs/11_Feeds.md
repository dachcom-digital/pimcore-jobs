# Feeds

![image](https://user-images.githubusercontent.com/700119/79235612-2032f680-7e6c-11ea-9665-7e41affbf9ba.png)

Some Connectors (Facebook for example) require a valid Feed-Url. 
Each connector is allowed to generated them within its own logic but the will end up with a structured route:

```bash
GET /jobs/[FEED_HOST]/[CONNECTOR_NAME]/[CONNECTOR_TOKEN]/feed/[FEED_ID]
```

Click on `Add Feed` to start the feed generating process.