<?php

namespace Nicc0\MockWebServer\Cache;

use Nicc0\MockWebServer\Interfaces\CacheTypeInterface;

/**
 * Class CacheAbstracr
 *
 * @since 15.08.2018
 * @author Daniel Tęcza
 * @package Nicc0\MockWebServer\Cache
 */
abstract class CacheAbstract implements CacheTypeInterface
{
    /**
     * @return array
     */
    public function toArray(): array
    {
        $result = [];

        foreach ($this as $key => $value) {
            $result[$key] = $value;
        }

        return $result;
    }

    /**
     * @return string
     */
    public function getClass(): string
    {
        return \get_class($this);
    }
}