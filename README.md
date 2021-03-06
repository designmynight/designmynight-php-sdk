# DesignMyNight PHP SDK

A simple Object Oriented wrapper for the DesignMyNight API, written with PHP.

More information can be found at [DesignMyNight API](http://developers.designmynight.com/).

## Requirements

* PHP >= 7.0
* [Guzzle](https://github.com/guzzle/guzzle) library,

## Install

The new version of `designmynight-php-sdk` using [Composer](http://getcomposer.org).
The first step to use `designmynight-php-sdk` is to download composer:

```bash
$ curl -s http://getcomposer.org/installer | php
```

Then run the following command to require the library:
```bash
$ php composer.phar require designmynight-php-sdk
```

## Laravel

Using [Laravel](https://laravel.com/)? Add the service provider in `config/app.php`:

```php
DesignMyNight/Laravel/DesignMyNightApiServiceProvider.php::class,
```

## Basic usage of `designmynight-php-sdk` client

```php
<?php

// This file is generated by Composer
require_once 'vendor/autoload.php';

$userId = '';
$apiKey = '';

$client = new \DesignMyNight\Client::create($userId, $apiKey);
$venues = $client->api('venues')->all();

# This returns the same as the above
$venues = $client->venues()->all();
```

You can access other DesignMyNight API endpoints through `$client` object

## License

`designmynight-php-sdk` is licensed under the MIT License - see the LICENSE file for details.