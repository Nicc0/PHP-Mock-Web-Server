<?php

namespace Nicc0\MockWebServer\Cache;

use Nicc0\MockWebServer\Exceptions\NotWritableDirectoryException;

/**
 * Class FileCache
 *
 * @since 15.08.2018
 * @author Daniel Tęcza
 * @package Nicc0\MockWebServer\Cache
 * @codeCoverageIgnore
 */
class FileCache extends CacheAbstract
{
    /** @var string */
    public const CACHE_TYPE = 'FileCache';

    /** @var string|null */
    private $tmpDir;

    /**
     * FileCache constructor.
     *
     * @param null|string $tmpDir
     */
    public function __construct(?string $tmpDir = null)
    {
        $this->tmpDir = $tmpDir;
    }

    /**
     * @return bool
     */
    public function init(): bool
    {
        $this->tmpDir = $this->getTmpDir($this->tmpDir);

        return true;
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
        $filename = $this->tmpDir . DIRECTORY_SEPARATOR . $cacheKey;

        $handle = fopen($filename, 'wb');

        if ($handle === false) {
            return false;
        }

        $success = fwrite($handle, $data);

        fclose($handle);

        return $success !== false;
    }

    /**
     * @param string $cacheKey
     * @return string
     */
    public function get(string $cacheKey): ?string
    {
        $filename = $this->tmpDir . DIRECTORY_SEPARATOR . $cacheKey;

        if (!\file_exists($filename)) {
            return null;
        }

        $handle = fopen($filename, 'rb');

        if ($handle === false) {
            return null;
        }

        $cacheData = '';

        while (!feof($handle)) {
            $cacheData .= fread($handle, 8192);
        }

        fclose($handle);

        return $cacheData;
    }

    /**
     * @param null|string $dir
     * @return string
     */
    private function getTmpDir(?string $dir = null): string
    {
        if ($dir === null) {
            $dir = sys_get_temp_dir() ?: '/tmp';
        }

        $dir = \trim($dir);

        if (!is_dir($dir) || !is_writable($dir) ) {
            throw new NotWritableDirectoryException();
        }

        $dir = \rtrim($dir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;

        if (\strpos($dir, 'PHPMockWebServer') === false) {
            $dir .= 'PHPMockWebServer' . DIRECTORY_SEPARATOR;

            $this->checkDir($dir);

            $dir .= \md5(\microtime() . rand(0, 2 ** 10));

            $this->checkDir($dir);
        }

        return $dir;
    }

    /**
     * @param $dir
     */
    private function checkDir($dir): void
    {
        if (!is_dir($dir) && mkdir($dir, 0777) && !is_dir($dir)) {
            throw new NotWritableDirectoryException();
        }
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'tmpDir' => $this->tmpDir,
        ];
    }
}