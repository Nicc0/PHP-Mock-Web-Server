<?php

namespace Nicc0\MockWebServer\Interfaces;

use Nicc0\MockWebServer\ResponseOptions;

/**
 * Interface ResponseInterface
 *
 * @since 15.08.2018
 * @author Daniel Tęcza
 * @package Nicc0\MockWebServer\Interfaces
 */
interface ResponseInterface extends \Serializable
{
    /**
     * @return string
     */
    public function getHash(): string;

    /**
     * @return float
     */
    public function getCreateTime(): float;

    /**
     * @return ResponseOptions|null
     */
    public function getOptions(): ?ResponseOptions;

    /**
     * @return array
     */
    public function toArray(): array;
}