<p align="center"><img alt="Mock Web Server for PHP 7.1" src="https://i.imgur.com/6NvxKzB.png"></p>

<p align="center">
 <a href="https://packagist.org/packages/nicc0/php-mock-web-server">
  <img alt="Latest Version on Packagist" src="https://img.shields.io/packagist/v/nicc0/php-mock-web-server.svg?style=flat-square">
 </a>
 <a href="https://github.com/Nicc0/PHP-Mock-Web-Server/blob/readme/LICENSE">
  <img alt="License" src="https://img.shields.io/github/license/Nicc0/PHP-Mock-Web-Server.svg?style=flat-square">
 </a>
 <a href="https://travis-ci.org/Nicc0/php-mock-web-server">
  <img alt="Build Status" src="https://img.shields.io/travis/Nicc0/PHP-Mock-Web-Server.svg?style=flat-square">
 </a>
 <a href="https://codecov.io/gh/Nicc0/php-mock-web-server">
  <img alt="Coverages" src="https://img.shields.io/codecov/c/github/nicc0/php-mock-web-server.svg?style=flat-square">
 </a>
</p>

## About PHP Mock Web Server


## Installation

PHP Mock Web Server is available on [Packagist](https://packagist.org/packages/nicc0/php-mock-web-server) and installation via Composer is the recommended way to install PHP Mock Web Server. Just run this command in your project repository:
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
    'body' => \json_encode([
      'status' => 200,
      'result' => 'Example response result',
      'error' => false,
    ], JSON_PRETTY_PRINT),
  ]);

  $response = new Response($responseOptions);

  if ($mockWebServer->setResponse('/test', $response)) {
    $url = $mockWebServer->getUrlForResponse('/test');
    $rawResponse = \file_get_contents($url);
    $customResponse = \json_decode($rawResponse, true);

    var_dump($customResponse);
  }
}

```
## Default Response
```json
{
    "host": "127.0.0.1",
    "port": 58389,
    "method": "GET",
    "status": 200,
    "root": "127.0.0.1:58389",
    "url": "http:\/\/127.0.0.1:58389\/",
    "uri": "\/",
    "headers": {
        "Host": "127.0.0.1:58389",
        "Connection": "close"
    },
    "post": [],
    "get": [],
    "server": {
        "DOCUMENT_ROOT": "\/home\/nicco\/www\/mockwebserver\/tests",
        "REMOTE_ADDR": "127.0.0.1",
        "REMOTE_PORT": "54157",
        "SERVER_SOFTWARE": "PHP 7.1.17-1+0~20180505045738.17+stretch~1.gbpde69c6 Development Server",
        "SERVER_PROTOCOL": "HTTP\/1.0",
        "SERVER_NAME": "127.0.0.1",
        "SERVER_PORT": "58389",
        "REQUEST_URI": "\/",
        "REQUEST_METHOD": "GET",
        "SCRIPT_NAME": "\/",
        "SCRIPT_FILENAME": "\/home\/nicco\/www\/mockwebserver\/server\/TempWebServer.php",
        "PHP_SELF": "\/",
        "HTTP_HOST": "127.0.0.1:58389",
        "HTTP_CONNECTION": "close",
        "REQUEST_TIME_FLOAT": 1537723564.846396,
        "REQUEST_TIME": 1537723564
    },
    "cache": {
        "type": "Nicc0\\MockWebServer\\Cache\\FileCache",
        "key": "7e969cbbec3e3da5f28a1eb9f6e67978"
    }
}
```

## License

The MIT License (MIT). Please see License File for more information. © Daniel "Nicc0" Tęcza
