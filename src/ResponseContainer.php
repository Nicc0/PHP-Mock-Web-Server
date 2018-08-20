<?php

namespace Nicc0\MockWebServer;

use Nicc0\MockWebServer\Exceptions\MockWebServerException;
use Nicc0\MockWebServer\Interfaces\ResponseInterface;

/**
 * Class ResponseContainer
 *
 * @since 15.08.2018
 * @author Daniel Tęcza
 * @package Nicc0\MockWebServer
 */
class ResponseContainer implements \Countable, \Serializable, \Iterator
{
    /** @var ResponseInterface[] */
    private $container = [];

    /**
     * ResponseContainer constructor.
     *
     * @param Response[] $stack
     */
    public function __construct(array $stack = [])
    {
        foreach ($stack as $key => $response) {
            if (!($response instanceof ResponseInterface)) {
                throw new MockWebServerException('Not valid response objects in $stack');
            }

            $this->addResponse($response);
        }
    }

    /**
     * @param ResponseContainer $responseContainer
     *
     * @return ResponseContainer
     */
    public function addResponsesFromContainer(ResponseContainer $responseContainer): ResponseContainer
    {
        if ($responseContainer->count() > 0) {
            foreach ($responseContainer as $response) {
                $this->addResponse($response);
            }
        }

        return $this;
    }

    /**
     * @param \Nicc0\MockWebServer\Interfaces\ResponseInterface $response
     */
    public function addResponse(ResponseInterface $response): void
    {
        $hash = $response->getHash();
        $this->container[$hash] = $response;
    }

    /**
     * @return int
     */
    public function count(): int
    {
        return \count($this->container);
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        $output = [];

        foreach ($this->container as $key => $response) {
            $output[$key] = $response->toArray();
        }

        return $output;
    }

    /**
     * @return string
     */
    public function serialize(): string
    {
        return \serialize(\array_values($this->container));
    }

    /**
     * @param string $serialized
     */
    public function unserialize($serialized)
    {
        $unserialized = \unserialize($serialized, ['allowed_classes' => [
            Response::class,
        ]]);

        /** @var Response $response */
        foreach($unserialized as $response) {
            $hash = $response->getHash();
            $this->container[$hash] = $response;
        }
    }

    public function rewind()
    {
        \reset($this->container);
    }

    public function current()
    {
        return \current($this->container);
    }

    public function key()
    {
        return \key($this->container);
    }

    public function next()
    {
        \next($this->container);
    }

    public function valid()
    {
        return \key($this->container) !== null;
    }
}