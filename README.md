LogRiver PHP SDK
============

[![Build Status](https://api.travis-ci.org/logriver/logriver-php-sdk.png)](https://travis-ci.org/logriver/logriver-php-sdk) [![Latest Stable Version](https://poser.pugx.org/logriver/logriver-php-sdk/v/stable.png)](https://packagist.org/packages/logriver/logriver-php-sdk)

The [LogRiver](https://logriver.io/) PHP SDK communicate with the LogRiver platform from your PHP application or script.

This library implements the [PSR-0](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-0.md), [PSR-1](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-1-basic-coding-standard.md) and [PSR-2](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-2-coding-style-guide.md).

Requirements
------------

* PHP 5.2
* PHP 5.3, 5.4, 5.5 or 5.6

Usage
-----

This part of code should be included at the top of your PHP project. It should be the first to be called:

```php
<?php
// Instantiate a new client with your API key
\Logriver\Client::init('[your_api_key]')->startListener();
```

And capture data where you want with:

```php
// Capture a message
\Logriver\Client::captureEvent("My message");

// Capture an error
\Logriver\Client::captureError("An error");

// Capture an exception
\Logriver\Client::captureException("An exception");
```

Installation
------------

### Install with Composer

The best way to install the library is by using [Composer](http://getcomposer.org). If you're using Composer to manage
dependencies, you can add the following to `composer.json` into the root of your project:

```json
{
  "require": {
    "logriver/logriver-php-sdk": "dev-master"
  }
}
```

Then, on the command line:

    # cd /my_lib_path/logriver-php-sdk/

And install the dependencies with:

    # curl -s http://getcomposer.org/installer | php
    # php composer.phar install
OR

    # composer install

Use the generated `vendor/autoload.php` file to autoload the library classes:

```php
require '/my_lib_path/logriver-php-sdk/vendor/autoload.php';
```

### Install source from GitHub

To install the source code:

    # git clone git://github.com/logriver/php-sdk.git

And including it using the autoloader:

```php
require '/my_lib_path/logriver-php-sdk/src/Logriver/Autoloader.php';
\Logriver\LogriverAutoloader::register();
```

With PHP 5.2 or unknow version
------------------------------

If you use PHP 5.2 or if you don't know the PHP version:

```php
<?php
require '/my_lib_path/logriver-php-sdk/src/LogriverAuto/Autoloader.php';
LogriverAuto_Autoloader::register();
Logriver_Client::init('[your_api_key]')->startListener();
```

```php
// Capture a message
Logriver_Client::captureEvent("My message");

// Capture an error
Logriver_Client::captureError("An error");

// Capture an exception
Logriver_Client::captureException("An exception");
```

License
-------

[![License](https://poser.pugx.org/logriver/logriver-php-sdk/license.png)](http://opensource.org/licenses/gpl-3.0.html)

logriver-php-sdk is licensed under the GPL V3 License - see the `LICENSE` file for details