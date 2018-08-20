<?php

declare(strict_types = 1);

namespace Nicc0\MockWebServer\Tests;

use Nicc0\MockWebServer\Cache\FileCache;
use Nicc0\MockWebServer\MockWebServer;
use Nicc0\MockWebServer\MockWebServerOptions;
use Nicc0\MockWebServer\Response;
use Nicc0\MockWebServer\ResponseConst;
use Nicc0\MockWebServer\ResponseContainer;
use Nicc0\MockWebServer\ResponseOptions;
use Nicc0\MockWebServer\Server\InternalResponse;

/**
 * Class MockWebServerTest
 *
 * @since 15.08.2018
 * @author Daniel Tęcza
 * @package Nicc0\MockWebServer\Tests
 */
class MockWebServerTest extends \PHPUnit\Framework\TestCase
{
    /** @var \Nicc0\MockWebServer\MockWebServerOptions */
    private $mockOptions;

    /** @var bool  */
    private $useStatic = true;

    /** @var null|int */
    private $usePort;

    public function setUp()
    {
        $cache = new FileCache();

        $options = $this->getMockBuilder(MockWebServerOptions::class)
            ->disableOriginalConstructor()
            ->getMock();

        $options->method('getHost')->willReturn('127.0.0.1');
        $options->method('getCache')->willReturn($cache);
        $options->method('getPhpVersion')->willReturn('php7.1');

        $useStatic = &$this->useStatic;
        $usePort = &$this->usePort;

        $options->method('isStatic')->will($this->returnCallback(
            function () use (&$useStatic) {
                return $useStatic ?? true;
            }
        ));

        $options->method('getPort')->will($this->returnCallback(
            function () use (&$usePort) {
                return $usePort ?? null;
            }
        ));

        $this->mockOptions = $options;
    }

    /**
     * @return \Nicc0\MockWebServer\MockWebServer
     */
    public function testCanCreateNewInstanceOfMockWebServer(): MockWebServer
    {
        $mockServer = new MockWebServer($this->mockOptions);

        $this->assertEquals('127.0.0.1', $mockServer->getHost());
        $this->assertGreaterThan(0, $mockServer->getPort());
        $this->assertCount(4, \parse_url($mockServer->getServerUrl()));
        $this->assertTrue($mockServer->isRunning());

        return $mockServer;
    }

    /**
     * @depends testCanCreateNewInstanceOfMockWebServer
     *
     * @param \Nicc0\MockWebServer\MockWebServer $defaultMockServer
     *
     * @return \Nicc0\MockWebServer\MockWebServer
     */
    public function testCanUseStaticInstanceOfMockWebServer(MockWebServer $defaultMockServer): MockWebServer
    {
        $mockServer = new MockWebServer($this->mockOptions);

        $this->assertTrue($mockServer->isRunning());
        $this->assertEquals($mockServer, $defaultMockServer);
        $this->assertEquals($mockServer->getServerHost(), $defaultMockServer->getServerHost());

        return $defaultMockServer;
    }

    /**
     * @depends testCanUseStaticInstanceOfMockWebServer
     *
     * @param \Nicc0\MockWebServer\MockWebServer $defaultMockServer
     *
     * @return \Nicc0\MockWebServer\MockWebServer
     */
    public function testCanCreateAnotherInstanceOfMockWebServer(MockWebServer $defaultMockServer): MockWebServer
    {
        $this->useStatic = false;

        $mockServer = new MockWebServer($this->mockOptions);

        $this->assertTrue($mockServer->isRunning());
        $this->assertNotEquals($mockServer, $defaultMockServer);
        $this->assertNotEquals($mockServer->getPort(), $defaultMockServer->getPort());

        return $mockServer;
    }

    /**
     * @depends testCanCreateAnotherInstanceOfMockWebServer
     *
     * @param \Nicc0\MockWebServer\MockWebServer $mockWebServer
     *
     * @expectedException \Nicc0\MockWebServer\Exceptions\MockWebServerException
     */
    public function testCanCreateAnotherInstanceOfMockWebServerOnTheSamePort(MockWebServer $mockWebServer): void
    {
        $this->useStatic = false;
        $this->usePort = $mockWebServer->getPort();

        new MockWebServer($this->mockOptions);
    }

    /**
     * @depends testCanCreateNewInstanceOfMockWebServer
     *
     * @param \Nicc0\MockWebServer\MockWebServer $mockServer
     */
    public function testCanMockWebServerWillSendDefaultResponse(MockWebServer $mockServer): void
    {
        $rawResponse = \file_get_contents($mockServer->getServerUrl());

        $this->assertGreaterThan(0, \strlen($rawResponse));

        $response = \json_decode($rawResponse, true);

        $this->assertInternalType('array', $response);
        $this->assertEquals(200, $response['status']);
        $this->assertEquals('GET', $response['method']);
    }

    /**
     * @depends testCanCreateNewInstanceOfMockWebServer
     *
     * @param \Nicc0\MockWebServer\MockWebServer $mockServer
     */
    public function testCanMockWebServerWillSendCustomResponseWhenItHasNotBeenSet(MockWebServer $mockServer): void
    {
        $context = \stream_context_create([
            'http' => [
                'ignore_errors' => true,
            ],
        ]);

        $rawResponse = \file_get_contents($mockServer->getRequestUrl('/notHasBeenSetResponse'), false, $context);

        $this->assertGreaterThan(0, \strlen($rawResponse));

        $response = \json_decode($rawResponse, true);

        $this->assertInternalType('array', $response);
        $this->assertEquals(404, $response['status']);
        $this->assertEquals('/notHasBeenSetResponse', $response['uri']);
    }

    /**
     * @depends testCanCreateNewInstanceOfMockWebServer
     *
     * @param \Nicc0\MockWebServer\MockWebServer $mockServer
     */
    public function testCanMockWebServerWillSetResponseAndSendEmptyResponse(MockWebServer $mockServer): void
    {
        $response = new Response();

        $this->assertTrue($mockServer->setResponse('/test', $response));

        $rawResponse = \file_get_contents($mockServer->getRequestUrl('/test'));

        echo($rawResponse);

        $this->assertInternalType('array', $http_response_header);
        $this->assertGreaterThan(0, \count($http_response_header));
        $this->assertEmpty($rawResponse);

        $headers = $this->getHeaders($http_response_header, $status);

        $this->assertEquals(200, $status);
        $this->assertEquals($mockServer->getHost() . ':' . $mockServer->getPort(), $headers['Host']);
        $this->assertEquals($response->getHash(), $headers['Response-Hash']);
        $this->assertEquals($mockServer->getServerUrl() . $response->getHash(), $mockServer->getRequestUrlWithHash($response));
    }

    /**
     * @depends testCanCreateNewInstanceOfMockWebServer
     *
     * @param \Nicc0\MockWebServer\MockWebServer $mockServer
     */
    public function testCanMockWebServerWillSetResponseWithTimeoutAndSendResponse(MockWebServer $mockServer): void
    {
        $options = new ResponseOptions();
        $options->setTimeout(2.5);

        $response = new Response($options);

        $this->assertTrue($mockServer->setResponse('/testWithTimeout', $response));

        $context = \stream_context_create([
            'http' => [
                'timeout' => 1,
            ]
        ]);

        $this->assertEmpty(\error_get_last());

        @\file_get_contents($mockServer->getRequestUrl('/testWithTimeout'), false, $context);

        $this->assertNotEmpty(\error_get_last());
    }

    /**
     * @depends testCanCreateNewInstanceOfMockWebServer
     *
     * @param \Nicc0\MockWebServer\MockWebServer $mockServer
     *
     * @return \Nicc0\MockWebServer\MockWebServer
     */
    public function testCanMockWebServerWillSetResponseAndSendCustomResponse(MockWebServer $mockServer): MockWebServer
    {
        $options = new ResponseOptions([
            'status' => 200,
            'method' => ResponseConst::METHOD_GET,
            'body' => \json_encode([
                'status' => 200,
                'error' => false,
                'result' => [
                    'foo' => 'bar',
                    'int' => 1337,
                ],
            ], JSON_PRETTY_PRINT),
        ]);

        $response = new Response($options);

        $this->assertTrue($mockServer->setResponse('/test', $response));

        $rawResponse = \file_get_contents($mockServer->getRequestUrl('/test'));
        $customResponse = \json_decode($rawResponse, true);

        $this->assertInternalType('array', $http_response_header);
        $this->assertGreaterThan(0, \count($http_response_header));

        $headers = $this->getHeaders($http_response_header, $status);

        $this->assertEquals(200, $status);
        $this->assertEquals($mockServer->getHost() . ':' . $mockServer->getPort(), $headers['Host']);
        $this->assertEquals($response->getHash(), $headers['Response-Hash']);

        $this->assertInternalType('array', $customResponse);
        $this->assertEquals(200, $customResponse['status']);
        $this->assertEquals(false, $customResponse['error']);
        $this->assertEquals('bar', $customResponse['result']['foo']);

        return $mockServer;
    }

    /**
     * @depends testCanMockWebServerWillSetResponseAndSendCustomResponse
     *
     * @param \Nicc0\MockWebServer\MockWebServer $mockServer
     *
     * @return \Nicc0\MockWebServer\MockWebServer
     */
    public function testCanMockWebServerWillAddAnotherResponseForExistsPath(MockWebServer $mockServer): MockWebServer
    {
        $options = new ResponseOptions([
            'status' => 201,
            'method' => ResponseConst::METHOD_POST,
            'body' => \json_encode([
                'status' => 404,
                'result' => false,
                'error' => false,
            ], JSON_PRETTY_PRINT),
        ]);

        $response = new Response($options);

        $this->assertTrue($mockServer->addResponse('/test', $response));

        $context = \stream_context_create([
            'http' => [
                'method' => 'POST',
            ]
        ]);

        $rawResponse = \file_get_contents($mockServer->getRequestUrl('/test'), false, $context);
        $customResponse = \json_decode($rawResponse, true);

        $this->assertInternalType('array', $http_response_header);
        $this->assertGreaterThan(0, \count($http_response_header));

        $headers = $this->getHeaders($http_response_header, $status);

        $this->assertEquals(201, $status);
        $this->assertEquals($mockServer->getHost() . ':' . $mockServer->getPort(), $headers['Host']);
        $this->assertEquals($response->getHash(), $headers['Response-Hash']);

        $this->assertInternalType('array', $customResponse);
        $this->assertEquals(404, $customResponse['status']);
        $this->assertEquals(false, $customResponse['error']);
        $this->assertEquals(false, $customResponse['result']);

        return $mockServer;
    }

    /**
     * @depends testCanCreateNewInstanceOfMockWebServer
     *
     * @param \Nicc0\MockWebServer\MockWebServer $mockServer
     *
     * @return \Nicc0\MockWebServer\MockWebServer
     */
    public function testCanMockWebServerWillSendDifferentCustomResponses(MockWebServer $mockServer): MockWebServer
    {
        $responses = new ResponseContainer([
            new Response(new ResponseOptions(['status' => 200])),
            new Response(new ResponseOptions(['status' => 404])),
        ]);

        $this->assertCount(2, $responses);

        $this->assertTrue($mockServer->addResponses('/differentResponses', new ResponseContainer()));
        $this->assertTrue($mockServer->setResponses('/differentResponses', $responses));

        return $mockServer;
    }

    public function testInternalResponse(): void
    {
        $response = new Response();
        $internalResponse = new InternalResponse($response);
        $newResponse = $internalResponse->getAsCommonResponse();

        $this->assertNotEquals($response, $newResponse);
        $this->assertEquals($response->getHash(), $newResponse->getHash());
        $this->assertEquals($response->getOptions(), $newResponse->getOptions());
        $this->assertNotEquals($response->getCreateTime(), $newResponse->getCreateTime());
        $this->assertGreaterThan($response->getCreateTime(), $newResponse->getCreateTime());
    }

    /**
     * @param $responseHeaders
     * @param int $status
     *
     * @return array
     */
    private function getHeaders($responseHeaders, &$status = 404): array
    {
        $headers = [];

        if (\strpos($responseHeaders[0], ':') === false) {
            $status = \explode(' ', \array_shift($responseHeaders), 3)[1];
        }

        foreach ($responseHeaders as $header) {
            try {
                $parsedHeader = \explode(':', $header, 2);
                [$headerName, $headerContent] = $parsedHeader;
                $headers[$headerName] = \trim($headerContent);
            } catch (\Throwable $ex) {
                continue;
            }
        }

        return $headers;
    }
}
