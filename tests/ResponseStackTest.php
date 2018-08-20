<?php

declare(strict_types = 1);

namespace Nicc0\MockWebServer\Tests;

use Nicc0\MockWebServer\Response;
use Nicc0\MockWebServer\ResponseOptions;
use Nicc0\MockWebServer\ResponseContainer;

/**
 * Class ResponseContainerTest
 *
 * @since 14.08.2018
 * @author Daniel Tęcza
 * @package Nicc0\MockWebServer\Tests
 */
class ResponseContainerTest extends \PHPUnit\Framework\TestCase
{

    /**
     * @return \Nicc0\MockWebServer\ResponseContainer
     */
    public function testCanCreateNewInstanceOfResponseContainerWithArrayOfResponses(): ResponseContainer
    {
        $responses = [
            new Response(new ResponseOptions(['status' => 200])),
            new Response(new ResponseOptions(['status' => 404])),
        ];

        $responseContainer = new ResponseContainer($responses);

        $this->assertCount(2, $responseContainer);

        return $responseContainer;
    }

    /**
     * @expectedException \Nicc0\MockWebServer\Exceptions\MockWebServerException
     */
    public function testCanCreateNewInstanceOfResponseContainerWithNotValidElementInArray(): void
    {
        new ResponseContainer([
            new \stdClass()
        ]);
    }

    /**
     * @return \Nicc0\MockWebServer\ResponseContainer
     */
    public function testCanAddResponseToResponseContainer(): ResponseContainer
    {
        $responseContainer = new ResponseContainer();
        $responseContainer->addResponse(new Response());

        $this->assertCount(1, $responseContainer);

        return $responseContainer;
    }

    /**
     * @depends testCanAddResponseToResponseContainer
     *
     * @param \Nicc0\MockWebServer\ResponseContainer $responseContainer
     *
     * @return \Nicc0\MockWebServer\ResponseContainer
     */
    public function testCanAddAnotherOneResponseToResponseContainer($responseContainer): ResponseContainer
    {
        $responseContainer->addResponse(new Response(new ResponseOptions(['status' => 200])));

        $this->assertCount(2, $responseContainer);

        return $responseContainer;
    }

    /**
     * @depends testCanCreateNewInstanceOfResponseContainerWithArrayOfResponses
     *
     * @param \Nicc0\MockWebServer\ResponseContainer $responseContainer
     */
    public function testCanResponseContainerIterable($responseContainer): void
    {
        $this->assertInstanceOf(\Iterator::class, $responseContainer);

        foreach ($responseContainer as $hash => $response) {
            $this->assertInstanceOf(Response::class, $response);
            $this->assertEquals($hash, $response->getHash());
        }
    }

    /**
     * @depends testCanCreateNewInstanceOfResponseContainerWithArrayOfResponses
     *
     * @param \Nicc0\MockWebServer\ResponseContainer $responseContainer
     */
    public function testCanConvertResponseContainerToArray(ResponseContainer $responseContainer): void
    {
        $array = $responseContainer->toArray();

        $this->assertCount(2, $array);

        foreach ($array as $hash => $response) {
            $this->assertArrayHasKey('hash', $response);
            $this->assertArrayHasKey('options', $response);
            $this->assertEquals($hash, $response['hash']);
        }
    }

    /**
     * @depends testCanCreateNewInstanceOfResponseContainerWithArrayOfResponses
     *
     * @param \Nicc0\MockWebServer\ResponseContainer $responseContainer
     */
    public function testCanAddAnotherResponseContainer(ResponseContainer $responseContainer): void
    {
        $anotherContainer = new ResponseContainer();
        $anotherContainer->addResponse(new Response(new ResponseOptions(['status' => 201])));

        $responseContainer->addResponsesFromContainer($anotherContainer);

        $this->assertCount(3, $responseContainer);
    }

    /**
     * @depends testCanAddAnotherOneResponseToResponseContainer
     *
     * @param \Nicc0\MockWebServer\ResponseContainer $ResponseContainer
     *
     * @return string
     */
    public function testCanSerializeResponseContainer($ResponseContainer): string
    {
        $this->assertInstanceOf(\Serializable::class, $ResponseContainer);

        $serializedResponseContainer = \serialize($ResponseContainer);

        $this->assertNotNull($serializedResponseContainer);
        $this->assertInternalType('string', $serializedResponseContainer);

        return $serializedResponseContainer;
    }

    /**
     * @depends testCanSerializeResponseContainer
     * @param string $serializedResponseContainer
     */
    public function testCanUnserializeResponse($serializedResponseContainer): void
    {
        /** @var Response $response */
        $response = \unserialize($serializedResponseContainer);

        $this->assertInstanceOf(ResponseContainer::class, $response);
    }
}