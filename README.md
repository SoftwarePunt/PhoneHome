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

This package is compatible with PHP 8.3+.

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

> [!NOTE]  
> We strongly recommend running under a user with limited privileges. These examples target `www-data` with `/var/www/` as home directory:
> 
> ```
> sudo su www-data -s /bin/bash
> ```

To do so, install it as a global composer package:
```bash
composer global require softwarepunt/phonehome --prefer-install=source
```

Then set up a cron job to run every minute (`crontab` example):
```bash
* * * * * (cd /var/www/.config/composer/vendor/softwarepunt/phonehome; TOKEN=SET_ME /var/www/.config/composer/vendor/bin/sp-phone-home)
```

## Providers
The following information is currently collected and sent:

### Environment
 - Server hostname
 - OS type and version (e.g. `Ubuntu 24.04.2 LTS (Noble Numbat) (Linux 6.8.0-55-generic)`)
 - PHP version
 - Working directory
 - Reboot required flag
 - Mounted storage devices and their capacity / usage

### Network
 - Public/outgoing (WAN) address, IPv4 and IPv6
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

### Status monitors

Project specific status monitors can be registered to collect additional information. The results of these monitors will be included in the collected data.