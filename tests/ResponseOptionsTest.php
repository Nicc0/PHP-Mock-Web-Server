<?php

declare(strict_types = 1);

namespace Nicc0\MockWebServer\Tests;

use Nicc0\MockWebServer\ResponseOptions;

/**
 * Class ResponseOptionsTest
 *
 * @since 14.08.2018
 * @author Daniel Tęcza
 * @package Nicc0\MockWebServer\Tests
 */
class ResponseOptionsTest extends \PHPUnit\Framework\TestCase
{

    /**
     * @return array
     */
    public function getMockDataOptions() : array
    {
        return [
            [
                [
                    'timeout' => 2,
                    'headers' => ['Content-Type' => 'application/json'],
                    'body' => '{"name":"John"}',
                    'status' => 200,
                    'method' => 'POST',
                ]
            ],
            [
                [
                    'timeout' => 0.25,
                    'headers' => ['Content-Type' => 'text/html; charset=utf-8'],
                    'body' => '<html><body><h1>HTML/1.0 404 Not Found</h1></body></html>',
                    'status' => 404,
                    'method' => 'GET',
                ]
            ],
        ];
    }

    /**
     * @dataProvider getMockDataOptions
     *
     * @param array $mockOptions
     */
    public function testCanSetOptionsBySetter(array $mockOptions): void
    {
        $options = new ResponseOptions();

        $timeout = $mockOptions['timeout'];
        $body    = $mockOptions['body'];
        $status  = $mockOptions['status'];
        $method  = $mockOptions['method'];

        $this->assertNull($options->getTimeout());
        $this->assertEquals($timeout, $options->setTimeout($timeout)->getTimeout());

        $this->assertNull($options->getBody());
        $this->assertEquals($body, $options->setBody($body)->getBody());

        $this->assertEmpty($options->getHeaders());
        $this->assertArrayHasKey('Content-Type', $options->setHeaders($mockOptions['headers'])->getHeaders());

        $this->assertNull($options->getStatus());
        $this->assertEquals($status, $options->setStatus($status)->getStatus());

        $this->assertNull($options->getMethod());
        $this->assertEquals($method, $options->setMethod($method)->getMethod());
    }

    /**
     * @dataProvider getMockDataOptions
     *
     * @param array $mockOptions
     */
    public function testCanSetOptionsByParamInConstructor(array $mockOptions): void
    {
        $options = new ResponseOptions($mockOptions);

        $this->assertInstanceOf(ResponseOptions::class, $options);
        $this->assertEquals($mockOptions['timeout'], $options->getTimeout());
        $this->assertEquals($mockOptions['status'], $options->getStatus());
        $this->assertEquals($mockOptions['body'], $options->getBody());
        $this->assertEquals($mockOptions['method'], $options->getMethod());
        $this->assertArrayHasKey('Content-Type', $options->getHeaders());
    }

    /**
     * @dataProvider getMockDataOptions
     *
     * @param array $mockOptions
     */
    public function testCanConvertOptionsToArray(array $mockOptions): void
    {
        $options = new ResponseOptions($mockOptions);

        $array = $options->toArray();

        $this->assertEquals($mockOptions['timeout'], $array['timeout']);
        $this->assertEquals($mockOptions['body'], $array['body']);
        $this->assertEquals($mockOptions['status'], $array['status']);
        $this->assertEquals($mockOptions['headers'], $array['headers']);
        $this->assertEquals($mockOptions['method'], $array['method']);
    }
}