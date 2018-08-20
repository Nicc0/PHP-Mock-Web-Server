<?php

namespace Nicc0\MockWebServer;

/**
 * Class RequestOptions
 *
 * @since 15.08.2018
 * @author Daniel Tęcza
 * @package Nicc0\MockWebServer
 */
class RequestOptions
{
    /** @var string */
    private $host;

    /** @var int */
    private $port;

    /** @var string */
    private $method;

    /** @var string */
    private $uri;

    /** @var array */
    private $server;

    /** @var array */
    private $get;

    /** @var array */
    private $post;

    /** @var array */
    private $headers;

    /** @var string */
    private $body;

    /**
     * Request constructor.
     *
     * @param string $host
     * @param int|null $port
     * @param array $options
     */
    public function __construct(string $host, ?int $port = null, array $options = [])
    {
        $this->host = $host;
        $this->port = $port;

        if (!empty($options)) {
            foreach (\get_object_vars($this) as $option => $value) {
                if ($this->$option === null && isset($options[$option])) {
                    $data = \in_array($option, ['get', 'post', 'server']) ? 'Data' : null;
                    $method = \sprintf('set%s%s', $data, ucfirst($option));
                    $this->$method($options[$option]);
                }
            }
        }
    }

    /**
     * @return string
     */
    public function getHost(): string
    {
        return $this->host;
    }

    /**
     * @return int|null
     */
    public function getPort(): ?int
    {
        return $this->port;
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
     * @return RequestOptions
     */
    public function setMethod(string $method): RequestOptions
    {
        $this->method = $method;
        return $this;
    }

    /**
     * @return string
     */
    public function getUri(): string
    {
        return $this->uri ?? '/';
    }

    /**
     * @param string $uri
     *
     * @return RequestOptions
     */
    public function setUri(string $uri): RequestOptions
    {
        $this->uri = $uri;
        return $this;
    }

    /**
     * @return array
     */
    public function getDataServer(): array
    {
        return $this->server ?? [];
    }

    /**
     * @param array $server
     *
     * @return RequestOptions
     */
    public function setDataServer(array $server): RequestOptions
    {
        $this->server = $server;
        return $this;
    }

    /**
     * @return array
     */
    public function getDataGet(): array
    {
        return $this->get ?? [];
    }

    /**
     * @param array $get
     *
     * @return RequestOptions
     */
    public function setDataGet(array $get): RequestOptions
    {
        $this->get = $get;
        return $this;
    }

    /**
     * @return array
     */
    public function getDataPost(): array
    {
        return $this->post ?? [];
    }

    /**
     * @param array $post
     *
     * @return RequestOptions
     */
    public function setDataPost(array $post): RequestOptions
    {
        $this->post = $post;
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
     * @return RequestOptions
     */
    public function setHeaders(array $headers): RequestOptions
    {
        $this->headers = $headers;
        return $this;
    }

    /**
     * @return string
     */
    public function getBody(): ?string
    {
        return $this->body;
    }

    /**
     * @param string $body
     *
     * @return RequestOptions
     */
    public function setBody(string $body): RequestOptions
    {
        $this->body = $body;
        return $this;
    }

}