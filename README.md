![License](https://img.shields.io/badge/license-MIT-blue?style=flat-square)
![PHP Version](https://img.shields.io/badge/php-%3E%3D8.3-777bb4?style=flat-square)

# Anonlytics Lib PHP

In a world with all GDPR rules, an IP address is now already labeled as personal information and can therefore not just be passed on by your website/application.

That's why we introduce Anonlytics, the server side analytics tool that only works with anonymized data for website analytics.

Entirely in accordance with all the rules of the GDPR. Our servers are all located in Europe and no IP addresses are used anywhere.

This library is part of https://anonlytics.eu, you need a (free) account to use this bundle for your website/application.

## Requirements

- PHP 8.3 or higher
- cURL extension
- JSON extension

## Installation

Use composer to add the library as dependency for your project:

```bash
composer require defixit/anonlytics-lib-php
```


## Usage

### Setup
At first, you need to create an account on [Anonlytics.eu](https://anonlytics.eu) to get your `client_token` and `site_token` to connect to our services.

After you created an account on our website you can create a file with the name `anonlytics.yaml` in the `config/packages/` folder, with the following content:


#### Send data to our service

```php
<?php

require 'vendor/autoload.php';

use DeFixIT\Anonlytics\Tracker;
use DeFixIT\Anonlytics\Exception\AnonlyticsDataNotSendException;

try {
    $tracker = new Tracker($_SERVER, $yourClientToken, $yourSiteToken);
    $response = $tracker->sendRequestData();
    
    if ($response) {
        echo "Analytics data sent successfully!";
    }
} catch (AnonlyticsDataNotSendException $e) {
    // Handle the exception as needed
    error_log("Failed to send analytics data: " . $e->getMessage());
}
```

For the `$requestArray` you can use `$_SERVER` in PHP. The response will be `true` or else we're throwing an `AnonlyticsDataNotSendException` exception.

## Features

- ✅ PHP 8.3+ compatible with modern features
- ✅ GDPR compliant - no personal data collection
- ✅ Respects "Do Not Track" headers
- ✅ Readonly classes for immutability
- ✅ Proper error handling and timeouts
- ✅ Type-safe with strict typing