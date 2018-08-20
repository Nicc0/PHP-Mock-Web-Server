<?php

declare(strict_types = 1);

namespace Nicc0\MockWebServer\Tests;

use Nicc0\MockWebServer\Cache\FileCache;
use Nicc0\MockWebServer\Cache\MemCache;
use Nicc0\MockWebServer\MockWebServerOptions;

/**
 * Class MockWebServerOptionsTest
 *
 * @since 15.08.2018
 * @author Daniel Tęcza
 * @package Nicc0\MockWebServer\Tests
 */
class MockWebServerOptionsTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @return array
     */
    public function getMockDataOptions() : array
    {
        return [
            [
                [
                    'host' => '198.168.1.1',
                    'port' => 11212,
                    'phpVersion' => 'php7.1',
                    'tmpDir' => __DIR__,
                    'static' => true,
                    'cache' => $this->getMockBuilder(FileCache::class)
                        ->disableOriginalConstructor()
                        ->getMock(),
                ]
            ],
            [
                [
                    'phpVersion' => 'php',
                    'host' => '1ocalhost',
                    'port' => null,
                    'tmpDir' => null,
                    'static' => false,
                    'cache' => $this->getMockBuilder(MemCache::class)
                        ->disableOriginalConstructor()
                        ->getMock(),
                ]
            ],
        ];
    }

    /**
     * @dataProvider getMockDataOptions
     *
     * @param array $mockOptions
     */
    public function testCanSetOptionsBySetter(array $mockOptions): void
    {
        $options = new MockWebServerOptions();

        $host       = $mockOptions['host'];
        $phpVersion = $mockOptions['phpVersion'];
        $tmpDir     = $mockOptions['tmpDir'];
        $static     = $mockOptions['static'];
        $cache      = $mockOptions['cache'];

        $this->assertEquals('127.0.0.1', $options->getHost());
        $this->assertEquals($host, $options->setHost($host)->getHost());

        $this->assertNull($options->getPort());
        $this->assertEquals(12321, $options->setPort(12321)->getPort());

        $this->assertNull($options->getPhpVersion());
        $this->assertEquals($phpVersion, $options->setPhpVersion($phpVersion)->getPhpVersion());

        $this->assertEmpty($options->isStatic());
        $this->assertEquals($static, $options->setStatic($static)->isStatic());

        $this->assertNull($options->getCache());
        $this->assertEquals($cache, $options->setCache($cache)->getCache());

        if ($mockOptions['tmpDir'] !== null) {
            $this->assertNull($options->getTmpDir());
            $this->assertEquals($tmpDir, $options->setTmpDir($tmpDir)->getTmpDir());
        }
    }

    /**
     * @dataProvider getMockDataOptions
     *
     * @param array $mockOptions
     */
    public function testCanSetOptionsByParamInConstructor(array $mockOptions): void
    {
        $options = new MockWebServerOptions('192.168.1.1', null, $mockOptions);

        $this->assertInstanceOf(MockWebServerOptions::class, $options);
        $this->assertEquals('192.168.1.1', $options->getHost());
        $this->assertEquals($mockOptions['phpVersion'], $options->getPhpVersion());
        $this->assertEquals($mockOptions['tmpDir'], $options->getTmpDir());
        $this->assertEquals($mockOptions['cache'], $options->getCache());
        $this->assertEquals($mockOptions['static'], $options->isStatic());

        if ($mockOptions['port'] === null) {
            $this->assertNull($options->getPort());
        }
    }
}