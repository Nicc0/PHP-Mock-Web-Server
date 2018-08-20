<?php

namespace Nicc0\MockWebServer;

use Nicc0\MockWebServer\Interfaces\CacheTypeInterface;

/**
 * Class MockWebServerOptions
 *
 * @since 15.08.2018
 * @author Daniel Tęcza
 * @package Nicc0\MockWebServer
 */
class MockWebServerOptions
{
    /** @var null|string */
    private $host;

    /** @var int|null */
    private $port;

    /** @var string */
    private $phpVersion;

    /** @var CacheTypeInterface */
    private $cache;

    /** @var string */
    private $tmpDir;

    /** @var bool */
    private $static;

    /**
     * MockWebServerOptions constructor.
     *
     * @param string|null $host
     * @param int|null $port
     * @param array $options
     */
    public function __construct(?string $host = '127.0.0.1', ?int $port = null, array $options = [])
    {
        $this->host = $host;
        $this->port = $port;

        if (!empty($options)) {
            foreach (\get_object_vars($this) as $option => $value) {
                if ($this->$option === null && isset($options[$option])) {
                    $this->$option = $options[$option];
                }
            }
        }
    }

    /**
     * @return null|string
     */
    public function getHost(): ?string
    {
        return $this->host;
    }

    /**
     * @param null|string $host
     *
     * @return MockWebServerOptions
     */
    public function setHost(?string $host): MockWebServerOptions
    {
        $this->host = $host;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getPort(): ?int
    {
        return $this->port;
    }

    /**
     * @param int|null $port
     *
     * @return MockWebServerOptions
     */
    public function setPort(?int $port): MockWebServerOptions
    {
        $this->port = $port;
        return $this;
    }

    /**
     * @return string
     */
    public function getPhpVersion(): ?string
    {
        return $this->phpVersion;
    }

    /**
     * @param string $phpVersion
     *
     * @return MockWebServerOptions
     */
    public function setPhpVersion(string $phpVersion): MockWebServerOptions
    {
        $this->phpVersion = $phpVersion;
        return $this;
    }

    /**
     * @return CacheTypeInterface
     */
    public function getCache(): ?CacheTypeInterface
    {
        return $this->cache;
    }

    /**
     * @param CacheTypeInterface $cache
     *
     * @return MockWebServerOptions
     */
    public function setCache(CacheTypeInterface $cache): MockWebServerOptions
    {
        $this->cache = $cache;
        return $this;
    }

    /**
     * @return string
     */
    public function getTmpDir(): ?string
    {
        return $this->tmpDir;
    }

    /**
     * @param string $tmpDir
     *
     * @return MockWebServerOptions
     */
    public function setTmpDir(string $tmpDir): MockWebServerOptions
    {
        $this->tmpDir = $tmpDir;
        return $this;
    }

    /**
     * @return bool
     */
    public function isStatic(): bool
    {
        return $this->static ?? false;
    }

    /**
     * @param bool $static
     *
     * @return MockWebServerOptions
     */
    public function setStatic(bool $static): MockWebServerOptions
    {
        $this->static = $static;
        return $this;
    }
}