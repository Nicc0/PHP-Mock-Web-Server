<?php

declare(strict_types = 1);

namespace Nicc0\MockWebServer\Tests;

use Nicc0\MockWebServer\Interfaces\ResponseInterface;
use Nicc0\MockWebServer\Response;
use Nicc0\MockWebServer\ResponseConst;
use Nicc0\MockWebServer\ResponseOptions;

/**
 * Class ResponseTest
 *
 * @since 14.08.2018
 * @author Daniel Tęcza
 * @package Nicc0\MockWebServer\Tests
 */
class ResponseTest extends \PHPUnit\Framework\TestCase
{
    /** @var \Nicc0\MockWebServer\ResponseOptions*/
    private $mockOptions;

    protected function setUp(): void
    {
        $this->mockOptions = new ResponseOptions([
            'timeout' => 2,
            'headers' => ['Content-Type' => 'application/json'],
            'body' => '{"name":"John"}',
            'status' => 200,
        ]);
    }

    /**
     * @return \Nicc0\MockWebServer\Response
     */
    public function testCheckIfResponseIsInstanceOfResponseInterface(): Response
    {
        $response = new Response($this->mockOptions);

        $this->assertInstanceOf(ResponseInterface::class, $response);

        return $response;
    }

    public function testCanGetHashOfResponse(): void
    {
        $response = new Response($this->mockOptions);

        $this->assertEquals(32, \strlen($response->getHash()));
    }

    /**
     * @depends testCheckIfResponseIsInstanceOfResponseInterface
     * @param \Nicc0\MockWebServer\Response $response
     */
    public function testCanGetResponseOptions($response): void
    {
        $this->assertEquals($this->mockOptions, $response->getOptions());
        $this->assertEquals(200, $response->getOptions()->getStatus());
    }

    /**
     * @depends testCheckIfResponseIsInstanceOfResponseInterface
     * @param \Nicc0\MockWebServer\Response $response
     */
    public function testCanConvertResponseToArray($response): void
    {
        $array = $response->toArray();

        $this->assertArrayHasKey('hash', $array);
        $this->assertArrayHasKey('options', $array);
    }

    /**
     * @depends testCheckIfResponseIsInstanceOfResponseInterface
     * @param \Nicc0\MockWebServer\Response $response
     * @return string
     */
    public function testCanSerializeResponse($response): string
    {
        $this->assertInstanceOf(\Serializable::class, $response);

        $serializedResponse = \serialize($response);

        $this->assertNotNull($serializedResponse);
        $this->assertInternalType('string', $serializedResponse);

        return $serializedResponse;
    }

    /**
     * @depends testCanSerializeResponse
     * @param $serializedResponse
     */
    public function testCanUnserializeResponse($serializedResponse): void
    {
        /** @var \Nicc0\MockWebServer\Response $response */
        $response = \unserialize($serializedResponse);

        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals(32, \strlen($response->getHash()));
    }

    public function testCheckIfResponseMethodExists(): void
    {
        $method = ResponseConst::METHOD_GET;
        $this->assertEquals($method, ResponseConst::getMethod('GET'));
    }

    public function testCheckIfResponseMethodNotExists(): void
    {
        $this->assertNull(ResponseConst::getMethod('DOWNLOAD'));
    }
}