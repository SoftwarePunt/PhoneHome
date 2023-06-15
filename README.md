# PhoneHome
**Software Punt module for remote monitoring and customer service utilities.**

This library provides "phone home" functionality for Software Punt projects. It is intended for managed services and SLA customers.

🪟 This project is open-source so we can be transparent about what data is collected and sent to our servers for monitoring purposes.

✉️ If you have any questions, contact us at [support@softwarepunt.nl](mailto:support@softwarepunt.nl).

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

$response = (new PhoneHome())
    ->setApiBaseUrl("https://sample.api.com/")
    ->setToken("set_api_token")
    ->setTimeout(30)
    ->send();

if ($response?->sla?->active)
    echo "Have active SLA!";
```

The server response will include SLA details if applicable to the caller.

A ping should be sent every minute or so for monitoring purposes. This is typically performed by a background task or cronjob.

## Standalone installation
You can set up PhoneHome as a standalone application without integrating it into existing software.

To do so, install it as a global composer package:
```bash
composer global require softwarepunt/phonehome
```

Then set up a cron job to run every minute (`crontab` example):
```bash
* * * * * (cd /root/.config/composer/vendor/softwarepunt/phonehome; TOKEN=SET_ME /root/.config/composer/vendor/bin/sp-phone-home)
```

## Providers
The following information is currently collected and sent:

### Environment
 - Server hostname
 - OS type and version (e.g. `Ubuntu 20.04.5 LTS (Focal Fossa) (Linux 4.15.0-184-generic x86_64)`)
 - PHP version
 - Working directory
 - Reboot required flag

### Network
 - Public (WAN) address
 - Private address

### Git version
 - Commit hash
 - Commit date/time

### Installed software versions
 - nginx
 - MySQL Server (`mysqld`)
 - Redis Server (`redis-server`)
 - .NET Runtime / SDK (`dotnet`)
 - Python (`python`)
 - Node.JS (`node`)
 - Composer (`composer`)
 - OpenSSL (`openssl`)
