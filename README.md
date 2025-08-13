# Oihana PHP - System

![Oihana PHP System](https://raw.githubusercontent.com/BcommeBois/oihana-php-system/main/assets/images/oihana-php-system-logo-inline-512x160.png)

Provides a standard set of PHP helpers and tools to create web and command-line applications.

[![Latest Version](https://img.shields.io/packagist/v/oihana/php-system.svg?style=flat-square)](https://packagist.org/packages/oihana/php-system)  
[![Total Downloads](https://img.shields.io/packagist/dt/oihana/php-system.svg?style=flat-square)](https://packagist.org/packages/oihana/php-system)  
[![License](https://img.shields.io/packagist/l/oihana/php-system.svg?style=flat-square)](LICENSE)

## 📚 Documentation

Full project documentation is available at:  
👉 https://bcommebois.github.io/oihana-php-system

## 📦 Installation

> Requires [PHP 8.4+](https://php.net/releases/) and ext-pdo

Install via Composer:
```bash
```

## ✨ Features

- Bootstrap helpers: initialize timezone, memory limit, error handling, and safe `ini_set` helpers
- Configuration: load TOML configuration files with sensible fallbacks
- Dependency Injection: convenience functions for building a PHP-DI container and loading definitions
- Logging: PSR-3 compatible lightweight file logger, plus Monolog config enums
- HTTP helpers: constants for methods, headers and parameter strategies
- MySQL utilities: DSN builder and a robust `PDO` connection builder with safe defaults
- Date utilities: `TimeInterval` to parse, format and convert durations

## 🚀 Quick start

```php
require __DIR__ . '/vendor/autoload.php';

use function oihana\init\{ initDefaultTimezone, initMemoryLimit, initErrors, initConfig, initDefinitions, initContainer };
use oihana\logging\Logger;
use oihana\db\mysql\MysqlPDOBuilder;

// Bootstrap
initDefaultTimezone('UTC');
initMemoryLimit('256M');
initErrors([
    'display_errors' => '1',
    'error_log'      => 'var/logs/php_errors.log',
], __DIR__);

// Load config and build the DI container
$config       = initConfig(__DIR__ . '/config', 'app.toml');
$definitions  = initDefinitions(__DIR__ . '/definitions');
$container    = initContainer($definitions, ['config' => $config]);

// Logger
$logger = new Logger(__DIR__ . '/var/logs', Logger::DEBUG);
$logger->info('Application started');

// MySQL PDO
$pdo = (new MysqlPDOBuilder([
    'host'     => '127.0.0.1',
    'dbname'   => 'demo',
    'username' => 'root',
    'password' => 'secret',
]))();
```

## 🧰 Usage

### Init helpers

```php
use function oihana\init\{ initDefaultTimezone, initMemoryLimit, initErrors, setIniIfExists };

initDefaultTimezone('UTC');
initMemoryLimit('512M');
initErrors([
    'display_errors'         => '1',
    'display_startup_errors' => '1',
    'error_log'              => 'var/logs/php_errors.log',
], __DIR__);

// Safe ini_set wrapper (no-op on empty values)
setIniIfExists('upload_max_filesize', '64M');
```

### Configuration and DI container

```php
use function oihana\init\{ initConfig, initDefinitions, initContainer };

$config      = initConfig(__DIR__ . '/config', 'app.toml');
$definitions = initDefinitions(__DIR__ . '/definitions');
$container   = initContainer($definitions, ['config' => $config]);
```

### Logging (PSR-3)

```php
use oihana\logging\Logger;

$logger = new Logger(__DIR__ . '/var/logs', Logger::INFO);
$logger->info('App started');
$logger->error('An error occurred: {error}', ['error' => 'boom']);

// Optional helpers
$logger->clear();             // remove all log files in log directory
$files = $logger->getLogFiles();
```

### MySQL PDO builder

```php
use oihana\db\mysql\MysqlPDOBuilder;

$pdo = (new MysqlPDOBuilder([
    'host'     => 'localhost',
    'dbname'   => 'test_db',
    'username' => 'user',
    'password' => 'secret',
    // 'validate'   => false, // disable validation if needed
    // 'skipDbName' => true,  // build DSN without dbname
]))();
```

### HTTP helpers

```php
use oihana\http\{ HttpMethod, HttpHeaders, HttpParamStrategy };

$method  = HttpMethod::POST;
$header  = HttpHeaders::CONTENT_TYPE;
$strategy = HttpParamStrategy::BOTH; // read from body and query
```

### TimeInterval (durations)

```php
use oihana\date\TimeInterval;

$d = new TimeInterval('1h 2m 5s');
echo $d->humanize();   // 1h 2m 5s
echo $d->formatted();  // 1:02:05
echo $d->toSeconds();  // 3725
```

## ✅ Running Unit Tests

To run all tests:
```bash
composer run-script test
```

To run a specific test file:
```bash
composer run test ./tests/oihana/date/TimeIntervalTest.php
```

## 🤝 Contributing

Contributions are welcome! Please:

- Open an issue for discussion before large changes
- Write tests for new features and bug fixes
- Run the full test suite locally before submitting a PR

## 🗒️ Changelog

See `CHANGELOG.md` for notable changes.

## 🧾 License

This project is licensed under the [Mozilla Public License 2.0 (MPL-2.0)](https://www.mozilla.org/en-US/MPL/2.0/).

## 👤 About the author

- Author: Marc ALCARAZ (aka eKameleon)
- Mail: marc@ooop.fr
- Website: http://www.ooop.fr

## 🛠️ Generate the Documentation

We use [phpDocumentor](https://phpdoc.org/) to generate the documentation into the `./docs` folder.

```bash
composer doc
```
## 🔗 Related packages

- `oihana/php-core` – core helpers and utilities used by this library: `https://github.com/BcommeBois/oihana-php-core`
- `oihana/php-exceptions` – a curated set of reusable custom exception classes for PHP: `https://github.com/BcommeBois/oihana-php-exceptions`
- `oihana/php-reflect` – reflection and hydration utilities: `https://github.com/BcommeBois/oihana-php-reflect`
- `oihana/php-enums` – a collection of strongly-typed constant enumerations for PHP: `https://github.com/BcommeBois/oihana-php-enums`
- `oihana/php-files` – a versatile PHP library for seamless and portable file and path handling: `https://github.com/BcommeBois/oihana-php-files`
