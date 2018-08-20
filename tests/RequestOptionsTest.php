<?php

declare(strict_types = 1);

namespace Nicc0\MockWebServer\Tests;

use Nicc0\MockWebServer\RequestOptions;

/**
 * Class RequestOptionsTest
 *
 * @since 14.08.2018
 * @author Daniel Tęcza
 * @package Nicc0\MockWebServer\Tests
 */
class RequestOptionsTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @return array
     */
    public function getMockDataOptions() : array
    {
        return [
            [
                'localhost',
                11211,
                [
                    'headers' => ['Content-Type' => 'application/json'],
                    'body' => '{"name":"John"}',
                    'server' => $_SERVER + ['HTTP_HOST' => '127.0.0.1:25565'],
                    'method' => 'GET',
                    'uri' => '/',
                ]
            ],
            [
                '127.0.0.1',
                null,
                [
                    'headers' => ['Content-Type' => 'text/html; charset=utf-8'],
                    'body' => '<html><body><h1>HTML/1.0 404 Not Found</h1></body></html>',
                    'server' => \array_merge($_SERVER, ['HTTP_HOST' => '127.0.0.1:25565']),
                    'method' => 'POST',
                    'uri' => '/test',
                ]
            ],
        ];
    }

    /**
     * @dataProvider getMockDataOptions
     *
     * @param string $host
     * @param int|null $port
     * @param array $mockOptions
     */
    public function testCanSetOptionsBySetter(string $host, ?int $port, array $mockOptions): void
    {
        $options = new RequestOptions($host, $port);

        $headers  = $mockOptions['headers'];
        $body     = $mockOptions['body'];
        $server   = $mockOptions['server'];
        $method   = $mockOptions['method'];
        $uri      = $mockOptions['uri'];

        $this->assertEquals($host, $options->getHost());
        $this->assertEquals($port, $options->getPort());
        $this->assertEquals($headers, $options->setHeaders($headers)->getHeaders());
        $this->assertEquals($body, $options->setBody($body)->getBody());
        $this->assertEquals($server, $options->setDataServer($server)->getDataServer());
        $this->assertEquals($method, $options->setMethod($method)->getMethod());
        $this->assertEquals($uri, $options->setUri($uri)->getUri());
    }

    /**
     * @dataProvider getMockDataOptions
     *
     * @param string $host
     * @param int|null $port
     * @param array $mockOptions
     */
    public function testCanSetOptionsByParamInConstructor(string $host, ?int $port, array $mockOptions): void
    {
        $options = new RequestOptions($host, $port, $mockOptions);

        $this->assertEquals($mockOptions['headers'], $options->getHeaders());
        $this->assertEquals($mockOptions['body'], $options->getBody());
        $this->assertEquals($mockOptions['server'], $options->getDataServer());
        $this->assertEquals($mockOptions['method'], $options->getMethod());
        $this->assertEquals($mockOptions['uri'], $options->getUri());
    }

    public function testCanSetDataValueOptions(): void
    {
        $options = new RequestOptions('localhost');

        $options->setDataGet(['get' => 'get'])
            ->setDataPost(['post' => 'post'])
            ->setDataServer(['server' => 'server']);

        $this->assertArrayHasKey('get', $options->getDataGet());
        $this->assertArrayHasKey('post', $options->getDataPost());
        $this->assertArrayHasKey('server', $options->getDataServer());
    }
}