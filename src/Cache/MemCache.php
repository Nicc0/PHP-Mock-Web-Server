<?php

namespace Nicc0\MockWebServer\Cache;

/**
 * Class MemCache
 *
 * @since 15.08.2018
 * @author Daniel Tęcza
 * @package Nicc0\MockWebServer\Cache
 * @codeCoverageIgnore
 */
class MemCache extends CacheAbstract
{
    /** @var string */
    public const CACHE_TYPE = 'MemCache';

    /** @var \Memcache */
    private $memcache;

    /** @var string */
    private $host;

    /** @var int */
    private $port;

    /**
     * FileCache constructor.
     *
     * @param string $host
     * @param int $port
     */
    public function __construct(string $host = 'localhost', int $port = 11211)
    {
        $this->host = $host;
        $this->port = $port;
    }

    public function init(): bool
    {
        $this->memcache = new \Memcache();

        $success = @$this->memcache->connect($this->host, $this->port);

        return $success;
    }

    /**
     * @param string $cacheKey
     * @param string $data
     * @param int $expire
     *
     * @return bool
     */
    public function set(string $cacheKey, string $data, int $expire = 60): bool
    {
        return $this->memcache->set($cacheKey, $data, MEMCACHE_COMPRESSED, $expire);
    }

    /**
     * @param string $cacheKey
     * @return string
     */
    public function get(string $cacheKey): ?string
    {
        return $this->memcache->get($cacheKey) ?: null;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'host' => $this->host,
            'port' => $this->port,
        ];
    }
}