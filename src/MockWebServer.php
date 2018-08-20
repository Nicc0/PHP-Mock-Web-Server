<?php

namespace Nicc0\MockWebServer;

use Nicc0\MockWebServer\Interfaces\ResponseInterface;
use Nicc0\MockWebServer\Server\InternalWebServer;

/**
 * Class MockWebServer
 *
 * @since 15.08.2018
 * @author Daniel Tęcza
 * @package Nicc0\MockWebServer
 */
class MockWebServer
{
    /** @var \Nicc0\MockWebServer\Server\InternalWebServer */
    private static $serverInstance;

    /** @var \Nicc0\MockWebServer\Server\InternalWebServer */
    private $server;

    /**
     * TestWebServer constructor.
     *
     * @param \Nicc0\MockWebServer\MockWebServerOptions $options
     */
    public function __construct(MockWebServerOptions $options) {
        if ($options->isStatic()) {
            $this->server = self::$serverInstance;
        }

        if ($this->server === null) {
            $this->server = new InternalWebServer($options);

            if ($options->isStatic()) {
                self::$serverInstance = $this->server;
            }
        }
    }

    /**
     * @return bool
     */
    public function isRunning(): bool
    {
        return $this->server->isRunning();
    }

    /**
     * @param string $path
     * @param \Nicc0\MockWebServer\Interfaces\ResponseInterface $response
     * @return bool
     */
    public function setResponse(string $path, ?ResponseInterface $response): bool
    {
        return $this->server->storeResponse($path, $response, true);
    }

    /**
     * @param string $path
     * @param \Nicc0\MockWebServer\ResponseContainer $responses
     *
     * @return bool
     */
    public function setResponses(string $path, ResponseContainer $responses): bool
    {
        return $this->server->storeResponses($path, $responses, true);
    }

    /**
     * @param string $path
     * @param \Nicc0\MockWebServer\Interfaces\ResponseInterface|null $response
     *
     * @return bool
     */
    public function addResponse(string $path, ?ResponseInterface $response): bool
    {
        return $this->server->storeResponse($path, $response, false);
    }

    /**
     * @param string $path
     * @param \Nicc0\MockWebServer\ResponseContainer $responses
     *
     * @return bool
     */
    public function addResponses(string $path, ResponseContainer $responses): bool
    {
        return $this->server->storeResponses($path, $responses, false);
    }

    /**
     * @return string
     */
    public function getServerHost(): string
    {
        return $this->server->getServerHost();
    }

    /**
     * @return string
     */
    public function getServerUrl(): string
    {
        return $this->server->getServerUrl();
    }

    /**
     * @param string $path
     *
     * @return string
     */
    public function getRequestUrl(string $path): string
    {
        return $this->server->getRequestUrl($path);
    }

    /**
     * @param \Nicc0\MockWebServer\Interfaces\ResponseInterface $response
     *
     * @return string
     */
    public function getRequestUrlWithHash(ResponseInterface $response): string
    {
        return $this->server->getServerUrl() . $response->getHash();
    }

    /**
     * Get the host of the server.
     *
     * @return string
     */
    public function getHost(): string
    {
        return $this->server->getHost();
    }

    /**
     * Get the port the network server is to be ran on.
     *
     * @return int
     */
    public function getPort(): int
    {
        return $this->server->getPort();
    }
}