# PhoneHome
**Software Punt module for remote monitoring and customer service utilities.**

This library provides "phone home" functionality for the Software Punt portal. It is intended for managed services and SLA customers.

## Installation
Install the package using [Composer](https://getcomposer.org/):

```bash
composer require softwarepunt/phonehome
```

This package is compatible with PHP 8.2+.

## Usage

Use the `PhoneHome` client to send a ping to the receiving API:

```php
use SoftwarePunt\PhoneHome\PhoneHome;

(new PhoneHome())
    ->setApiBaseUrl("https://sample.api.com/")
    ->setToken("set_api_token")
    ->setTimeout(30)
    ->send();
```

## Providers
The following information is currently collected and sent:

### Environment
 - Server hostname
 - OS type and version
 - PHP version
 - Working directory

### Network
 - Public (WAN) address
 - Private address

### Git version
 - Commit hash
 - Commit date/time