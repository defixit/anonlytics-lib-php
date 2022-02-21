![License](https://img.shields.io/badge/license-MIT-blue?style=flat-square)
# Anonlytics Lib PHP
In a world with all GDPR rules, an IP address is now already labeled as personal information and can therefore not just be passed on by your website/application.

That's why we introduce Anonlytics, the server side analytics tool that only works with anonymized data for website analytics.

Entirely in accordance with all the rules of the GDPR. Our servers are all located in Europe and no IP addresses are used anywhere.

This library is part of https://anonlytics.eu, you need a (free) account to use this bundle for your website/application.

## Installation
Use composer to add the library as dependency for your project

`composer require defixit/anonlytics-lib-php`


## Usage

### Setup
At first, you need to create an account on [Anonlytics.eu](https://anonlytics.eu) to get your `client_token` and `site_token` to connect to our services.

After you created an account on our website you can create a file with the name `anonlytics.yaml` in the `config/packages/` folder, with the following content:


#### Send data to our service
```php
<?php

require 'vendor/autoload.php';

use DeFixIT\Anonlytics\Tracker;

$tracker = new Tracker($requestArray, $yourClientToken, $yourSiteToken)

$response = $tracker->sendRequestData();
```

For the `$requestArray` you can use `$_SERVER` in PHP. The response will be `true` or else we're throwing an `AnonlyticsDataNotSendException` exception.