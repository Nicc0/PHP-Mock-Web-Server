<?php

namespace Nicc0\MockWebServer\TempServer;

TempWebServer::autoload();

use Nicc0\MockWebServer\Exceptions\MockWebServerException;
use Nicc0\MockWebServer\Interfaces\CacheTypeInterface;
use Nicc0\MockWebServer\RequestOptions;
use Nicc0\MockWebServer\ResponseConst;
use Nicc0\MockWebServer\Server\InternalTempWebServer;
use Nicc0\MockWebServer\Server\InternalWebServer;

/**
 * Class TempWebServer
 *
 * @internal
 * @since 15.08.2018
 * @author Daniel Tęcza
 * @package Nicc0\MockWebServer\TempServer
 */
class TempWebServer
{
    /**
     * TempWebServer constructor.
     */
    public function __construct()
    {
        $headers = \function_exists('getallheaders') ? \getallheaders() : $this->getAllHeaders();
        $input   = \file_get_contents('php://input');

        $requestMethod = ResponseConst::getMethod($_SERVER['REQUEST_METHOD']);

        if ($requestMethod === null) {
            throw new MockWebServerException('Invalid request method');
        }

        $request = new RequestOptions($_SERVER['SERVER_NAME'], $_SERVER['SERVER_PORT']);
        $request->setMethod($requestMethod);
        $request->setUri($_SERVER['REQUEST_URI']);
        $request->setDataServer($_SERVER);
        $request->setDataGet($_GET);
        $request->setDataPost($_POST);
        $request->setHeaders($headers);
        $request->setBody($input);

        $cache = $this->getCacheFromEnvVariable();

        $webServer = new InternalTempWebServer($cache);
        $webServer->printResponse($request);
    }

    /**
     * @return CacheTypeInterface
     */
    private function getCacheFromEnvVariable(): CacheTypeInterface
    {
        $cache = \getenv(InternalWebServer::ENV_VAR);

        [$cacheClass, $cacheData] = \explode('|', $cache);

        if (!\class_exists($cacheClass)) {
            throw new MockWebServerException('Cache Class deosn\'t exists');
        }

        $arguments = \unserialize($cacheData, ['allowed_classes' => false]);

        try {
            $reflection = new \ReflectionClass($cacheClass);
            $cache = $reflection->newInstanceArgs($arguments);
        } catch (\ReflectionException $ex) {
            throw new MockWebServerException($ex->getMessage(), $ex->getCode(), $ex->getPrevious());
        }

        if ($cache instanceof CacheTypeInterface) {
            return $cache;
        }

        throw new MockWebServerException('Cache Class isn\'t instance of CacheTypeInterface');
    }

    /**
     * @return array
     */
    private function getAllHeaders(): array
    {
        $headers = [];
        $copy_server = [
            'CONTENT_TYPE'   => 'Content-Type',
            'CONTENT_LENGTH' => 'Content-Length',
            'CONTENT_MD5'    => 'Content-Md5',
        ];

        foreach ($_SERVER as $key => $value) {
            if (\strpos($key, 'HTTP_') === 0) {
                $key = \substr($key, 5);
                if (!isset($copy_server[$key], $_SERVER[$key])) {
                    $key = \str_replace(' ', '-', \ucwords(\strtolower(\str_replace('_', ' ', $key))));
                    $headers[$key] = $value;
                }
            } elseif (isset($copy_server[$key])) {
                $headers[$copy_server[$key]] = $value;
            }
        }

        if (!isset($headers['Authorization'])) {
            if (isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION'])) {
                $headers['Authorization'] = $_SERVER['REDIRECT_HTTP_AUTHORIZATION'];
            } elseif (isset($_SERVER['PHP_AUTH_USER'])) {
                $basic_pass = $_SERVER['PHP_AUTH_PW'] ?? '';
                $headers['Authorization'] = 'Basic ' . \base64_encode($_SERVER['PHP_AUTH_USER'] . ':' . $basic_pass);
            } elseif (isset($_SERVER['PHP_AUTH_DIGEST'])) {
                $headers['Authorization'] = $_SERVER['PHP_AUTH_DIGEST'];
            }
        }

        return $headers;
    }

    public static function autoload(): void
    {
        $autoloadFiles = [
            \dirname(__DIR__) . '/vendor/autoload.php',
            \dirname(__DIR__, 2) . '/autoload.php'
        ];

        foreach ($autoloadFiles as $file) {
            if (\file_exists($file)) {
                require_once $file; break;
            }
        }
    }
}

try {
    new TempWebServer();
} catch (\Throwable $ex) {
    echo ($ex);
}
