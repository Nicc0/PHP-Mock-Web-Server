<?php

declare(strict_types = 1);

namespace Nicc0\MockWebServer\Tests;

use Nicc0\MockWebServer\Cache\CacheAbstract;
use Nicc0\MockWebServer\Interfaces\CacheTypeInterface;

/**
 * Class MemCacheTest
 *
 * @since 14.08.2018
 * @author Daniel Tęcza
 * @package Nicc0\MockWebServer\Tests
 */
class CacheTest extends \PHPUnit\Framework\TestCase
{

    /**
     * @return \Nicc0\MockWebServer\Cache\CacheAbstract
     */
    public function getMockCache(): CacheAbstract
    {
        return new class extends CacheAbstract {

            protected $cache;

            public function init(): bool
            {
                $this->cache = [];
                return true;
            }

            public function set(string $cacheKey, string $data, int $expire = 60): bool
            {
                $this->cache[$cacheKey] = $data;
                return true;
            }

            public function get(string $cacheKey): ?string
            {
                return $this->cache[$cacheKey] ?? null;
            }
        };
    }

    /**
     * @return \Nicc0\MockWebServer\Cache\CacheAbstract
     */
    public function testCheckIfCacheAbstractIsInstanceOfCacheTypeInterface(): CacheAbstract
    {
        $cache = $this->getMockCache();

        $this->assertInstanceOf(CacheTypeInterface::class, $cache);

        return $cache;
    }

    /**
     * @depends testCheckIfCacheAbstractIsInstanceOfCacheTypeInterface
     *
     * @param \Nicc0\MockWebServer\Interfaces\CacheTypeInterface $cache
     *
     * @return \Nicc0\MockWebServer\Cache\MemCache
     */
    public function testCanUseMethodInitInCacheAbstract(CacheTypeInterface $cache): CacheTypeInterface
    {
        $this->assertTrue($cache->init());

        return $cache;
    }

    /**
     * @depends testCanUseMethodInitInCacheAbstract
     *
     * @param \Nicc0\MockWebServer\Interfaces\CacheTypeInterface $cache
     *
     * @return \Nicc0\MockWebServer\Cache\MemCache
     */
    public function testCanSetDataInCache(CacheTypeInterface $cache): CacheTypeInterface
    {
        $this->assertTrue($cache->set(md5(__CLASS__ ?: 'MemCacheTest'), (__CLASS__  ?: 'Test') . 'Value', 1));

        return $cache;
    }

    /**
     * @depends testCanSetDataInCache
     *
     * @param \Nicc0\MockWebServer\Interfaces\CacheTypeInterface $cache
     */
    public function testCanGetDataFromCache(CacheTypeInterface $cache): void
    {
        $this->assertEquals((__CLASS__  ?: 'Test') . 'Value', $cache->get(md5(__CLASS__ ?: 'MemCacheTest')));
    }

    /**
     * @depends testCanSetDataInCache
     *
     * @param \Nicc0\MockWebServer\Interfaces\CacheTypeInterface $cache
     */
    public function testCanConvertCacheToArray(CacheTypeInterface $cache): void
    {
        $array = $cache->toArray();

        $this->assertArrayHasKey('cache', $array);
        $this->assertCount(1, $array['cache']);
    }

    /**
     * @depends testCheckIfCacheAbstractIsInstanceOfCacheTypeInterface
     *
     * @param \Nicc0\MockWebServer\Interfaces\CacheTypeInterface $cache
     */
    public function testCanGetClassOfCache(CacheTypeInterface $cache) : void
    {
        $this->assertEquals(\get_class($cache), $cache->getClass());
    }
}
