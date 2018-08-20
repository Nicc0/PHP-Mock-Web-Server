<?php

namespace Nicc0\MockWebServer\Server;

use Nicc0\MockWebServer\Interfaces\CacheTypeInterface;
use Nicc0\MockWebServer\Interfaces\ResponseInterface;
use Nicc0\MockWebServer\RequestOptions;
use Nicc0\MockWebServer\Response;
use Nicc0\MockWebServer\ResponseContainer;
use Nicc0\MockWebServer\ResponseOptions;

/**
 * Class InternalTempWebServer
 *
 * @since 15.08.2018
 * @author Daniel Tęcza
 * @package Nicc0\MockWebServer\Server
 * @codeCoverageIgnore
 */
class InternalTempWebServer
{
    /** @var \Nicc0\MockWebServer\Interfaces\CacheTypeInterface */
    private $cache;

    /**
     * InternalTempWebServer constructor.
     *
     * @param \Nicc0\MockWebServer\Interfaces\CacheTypeInterface $cache
     */
    public function __construct(CacheTypeInterface $cache)
    {
        $this->cache = $cache;
        $this->cache->init();
    }

    /**
     * @param \Nicc0\MockWebServer\RequestOptions $request
     */
    public function printResponse(RequestOptions $request): void
    {
        $response = $this->getResponse($request);
        $options = $response->getOptions();

        if ($options->getTimeout() > 0) {
            \usleep($options->getTimeout() * (10 ** 6));
        }

        \header('Response-Hash: ' . $response->getHash());

        \http_response_code($options->getStatus() ?? 200);

        foreach ($options->getHeaders() as $key => $value) {
            \header(\sprintf('%s: %s', $key, $value), true);
        }

        die($options->getBody());
    }

    /**
     * @param \Nicc0\MockWebServer\RequestOptions $request
     *
     * @return \Nicc0\MockWebServer\Response
     */
    private function getResponse(RequestOptions $request): Response
    {
        if ($request->getUri() === '/') {
            return $this->getDefaultResponse($request);
        }

        $cacheKey  = $this->getCacheKey($request);
        $cacheData = $this->cache->get($cacheKey);

        if ($cacheData === null) {
            return $this->getDefaultResponse($request, 404);
        }

        /** @var \Nicc0\MockWebServer\ResponseContainer $responseContainer */
        $responseContainer = \unserialize($cacheData, ['allowed_classes' => [
            ResponseContainer::class,
        ]]);

        /** @var \Nicc0\MockWebServer\Response $currentResponse */
        $currentResponse = null;

        /** @var \Nicc0\MockWebServer\Response $response */
        foreach ($responseContainer as $hash => $response) {
            $options = $response->getOptions();
            $method = $options->getMethod();

            if ($method !== null && $method !== $request->getMethod()) {
                continue;
            }

            if ($currentResponse === null || $currentResponse->getCreateTime() > $response->getCreateTime()) {
                $currentResponse = $response; break;
            }
        }

        if ($currentResponse === null) {
            return $this->getDefaultResponse($request, 404);
        }

        $newContainer = new ResponseContainer();

        foreach ($responseContainer as $hash => $response) {
            if ($currentResponse->getHash() !== $hash) {
                $newContainer->addResponse($response);
            }
        }

        $newResponse = new InternalResponse($currentResponse);

        $newContainer->addResponse($newResponse->getAsCommonResponse());

        $this->cache->set($cacheKey, \serialize($newContainer));

        return $currentResponse;
    }

    /**
     * @param \Nicc0\MockWebServer\RequestOptions $request
     * @param int $status
     *
     * @return \Nicc0\MockWebServer\Response
     */
    private function getDefaultResponse(RequestOptions $request, int $status = 200): Response
    {
        $body = \json_encode([
            'host'    => $request->getHost(),
            'port'    => $request->getPort(),
            'method'  => $request->getMethod(),
            'status'  => $status,
            'root'    => $this->getServerHost($request),
            'url'     => $this->getUrlForRequest($request),
            'uri'     => $request->getUri(),
            'headers' => $request->getHeaders(),
            'post'    => $request->getDataPost(),
            'get'     => $request->getDataGet(),
            'server'  => $request->getDataServer(),
            'cache'   => [
                'type'  => $this->cache->getClass(),
                'key'   => $this->getCacheKey($request)
            ],
        ], JSON_PRETTY_PRINT);

        $options = new ResponseOptions();
        $options->setStatus($status);
        $options->setHeaders(['Content-Type' => 'application/json']);
        $options->setBody($body);

        return new Response($options);
    }

    /**
     * @param \Nicc0\MockWebServer\RequestOptions $options
     *
     * @return string
     */
    private function getServerHost(RequestOptions $options): string
    {
        return \implode(':', [$options->getHost(), $options->getPort()]);
    }

    /**
     * @param \Nicc0\MockWebServer\RequestOptions $options
     *
     * @return string
     */
    private function getUrlForServer(RequestOptions $options): string
    {
        return \sprintf('http://%s/', $this->getServerHost($options));
    }

    /**
     * @param \Nicc0\MockWebServer\RequestOptions $options
     *
     * @return string
     */
    private function getUrlForRequest(RequestOptions $options): string
    {
        return \rtrim($this->getUrlForServer($options), '/') . $options->getUri();
    }

    /**
     * @param \Nicc0\MockWebServer\RequestOptions $request
     *
     * @return string
     */
    private function getCacheKey(RequestOptions $request): string
    {
        return \md5($this->getUrlForRequest($request));
    }
}