# Mock Web Server for PHP 7.1

[![Build Status](https://img.shields.io/travis/Nicc0/php-mock-web-server.svg?style=flat-square)](https://travis-ci.org/Nicc0/php-mock-web-server) [![Codecov](https://img.shields.io/codecov/c/github/nicc0/php-mock-web-server.svg?style=flat-square)](https://codecov.io/gh/Nicc0/php-mock-web-server)

## Library Features

 - List item
 -  
 - Compatible with PHP 7.1 and later
 - And much more

## Requirements

 - PHP 7.1
 - ext-json
 - ext-sockets
 - ext-ctype

## Installation

PHP Mock Web Server is available on [Packagist](https://packagist.org/packages/nicc0/php-mock-web-server) and installation via Composer is the recommended way to install PHP Mock Web Server. Just add this line to your `composer.json` file:
```json
"nicc0/php-mock-web-server": "~1.0.0"
```
or run
```sh
composer require-dev nicc0/php-mock-web-server
```
## A Simple Example
```php
<?php

$options = new MockWebServerOptions();
$options->setCache(new FileCache());
$options->setStatic(false);

$mockWebServer = new MockWebServer($options);

if ($mockWebServer->isRunning()) {
  $responseOptions = new ResponseOptions([
    'status' => 200,
    'method' => ResponseConst::METHOD_GET,
  ]);

  $response = new Response($responseOptions);

  if ($mockWebServer->setResponse('/test', $response)) {
    $url = $reponse->getUrlForResponse();
  }
}

```
## Example Responses
