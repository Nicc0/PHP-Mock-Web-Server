<?php

namespace Nicc0\MockWebServer;

use Nicc0\MockWebServer\Interfaces\ResponseInterface;

/**
 * Class Response
 *
 * @since 15.08.2018
 * @author Daniel Tęcza
 * @package Nicc0\MockWebServer
 */
class Response implements ResponseInterface
{
    /** @var ResponseOptions */
    protected $options;

    /** @var string */
    protected $hash;

    /** @var float */
    protected $time;

    /**
     * Response constructor.

     * @param ResponseOptions $options
     */
    public function __construct(?ResponseOptions $options = null)
    {
        $this->time = \microtime(true);
        $this->options = $options !== null ? clone $options : new ResponseOptions();
        $this->hash = \md5($this->time . \serialize($this->options->toArray()));
    }

    /**
     * @return ResponseOptions
     */
    public function getOptions(): ResponseOptions
    {
        return $this->options;
    }

    /**
     * @return string
     */
    public function getHash(): string
    {
        return $this->hash;
    }

    /**
     * @return float
     */
    public function getCreateTime(): float
    {
        return $this->time;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'hash'     => $this->getHash(),
            'time'     => $this->getCreateTime(),
            'options'  => $this->options->toArray()
        ];
    }

    /**
     * @return string
     */
    public function serialize() : string
    {
        return \serialize($this->toArray());
    }

    /**
     * @param string $serialized
     */
    public function unserialize($serialized): void
    {
        $unserialize = \unserialize($serialized, ['allowed_classes' => false]);

        $this->hash = $unserialize['hash'];
        $this->time = $unserialize['time'];
        $this->options = new ResponseOptions($unserialize['options']);
    }
}