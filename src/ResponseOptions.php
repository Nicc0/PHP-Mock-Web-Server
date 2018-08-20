<?php

namespace Nicc0\MockWebServer;

/**
 * Class ResponseOptions
 *
 * @since 15.08.2018
 * @author Daniel Tęcza
 * @package Nicc0\MockWebServer
 */
class ResponseOptions
{
    /** @var float */
    private $timeout;

    /** @var array */
    private $headers;

    /** @var mixed */
    private $body;

    /** @var int */
    private $status;

    /** @var string */
    private $method;

    /**
     * ResponseOptions constructor.
     * @param array $options
     */
    public function __construct(array $options = [])
    {
        if (!empty($options)) {
            foreach (\get_object_vars($this) as $option => $value) {
                if ($this->$option === null && isset($options[$option])) {
                    $method = \sprintf('set%s', ucfirst($option));
                    $this->$method($options[$option]);
                }
            }
        }
    }

    /**
     * @return float|null
     */
    public function getTimeout(): ?float
    {
        return $this->timeout;
    }

    /**
     * @param float $seconds Timeout in seconds
     * @return ResponseOptions
     */
    public function setTimeout(float $seconds): ResponseOptions
    {
        $this->timeout = $seconds;
        return $this;
    }

    /**
     * @return array
     */
    public function getHeaders(): array
    {
        return $this->headers ?? [];
    }

    /**
     * @param array $headers
     *
     * @return ResponseOptions
     */
    public function setHeaders(array $headers): ResponseOptions
    {
        $this->headers = $headers;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * @param mixed $body
     *
     * @return ResponseOptions
     */
    public function setBody($body): ResponseOptions
    {
        $this->body = $body;
        return $this;
    }

    /**
     * @return int
     */
    public function getStatus(): ?int
    {
        return $this->status;
    }

    /**
     * @param int $status
     *
     * @return ResponseOptions
     */
    public function setStatus(int $status): ResponseOptions
    {
        $this->status = $status;
        return $this;
    }

    /**
     * @return string
     */
    public function getMethod(): ?string
    {
        return $this->method;
    }

    /**
     * @param string $method
     *
     * @return ResponseOptions
     */
    public function setMethod(string $method): ResponseOptions
    {
        $this->method = $method;
        return $this;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'timeout' => $this->timeout,
            'method' => $this->method,
            'headers' => $this->headers,
            'body' => $this->body,
            'status' => $this->status,
        ];
    }
}