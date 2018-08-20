<?php

namespace Nicc0\MockWebServer\Server;

use Nicc0\MockWebServer\Cache\FileCache;
use Nicc0\MockWebServer\Exceptions\CouldNotBindPortToAddressException;
use Nicc0\MockWebServer\Exceptions\MockWebServerException;
use Nicc0\MockWebServer\Exceptions\NotFoundOpenPortException;
use Nicc0\MockWebServer\Interfaces\CacheTypeInterface;
use Nicc0\MockWebServer\Interfaces\ResponseInterface;
use Nicc0\MockWebServer\MockWebServerOptions;
use Nicc0\MockWebServer\Response;
use Nicc0\MockWebServer\ResponseContainer;

/**
 * Class InternalWebServer
 *
 * @since 15.08.2018
 * @author Daniel Tęcza
 * @package Nicc0\MockWebServer\Server
 */
class InternalWebServer
{
    /** @var string */
    public const ENV_VAR = 'PHP_MockWebServer';

    /** @var string */
    public const SEP = DIRECTORY_SEPARATOR;

    /** @var string */
    private $host;

    /** @var int */
    private $port;

    /** @var CacheTypeInterface  */
    private $cache;

    /** @var string */
    private $php;

    /** @var int */
    private $pid;

    /**
     * InternalWebServer constructor.
     *
     * @param MockWebServerOptions $options
     */
    public function __construct(MockWebServerOptions $options)
    {
        $this->host  = $options->getHost();
        $this->port  = $options->getPort() ?? $this->findOpenPort();
        $this->cache = $options->getCache() ?? new FileCache();
        $this->php   = $options->getPhpVersion() ?? 'php';

        $this->cache->init();
        $this->startServer();
    }

    private function startServer(): bool
    {
        if ($this->isRunning()) {
            return false;
        }

        if (!\putenv(self::ENV_VAR . '=' . $this->cache->getClass() . '|' . $this->getCacheParams())) {
            throw new MockWebServerException('Unable to put environmental variable');
        }

        $cmd = $this->getShellCommand();

        $this->pid = \exec($cmd, $o, $ret);

        if (!\ctype_digit($this->pid)) {
            throw new MockWebServerException("Error starting server, received '{$this->pid}', expected int PID");
        }

        \sleep(1);

        if (!$this->isRunning()) {
            throw new MockWebServerException("Failed to start server. Is something already running on port {$this->port}?");
        }

        \register_shutdown_function(function () {
            if ($this->isRunning()) {
                $this->stopServer();
            }
        });

        return true;
    }

    private function stopServer(): void
    {
        if ($this->pid !== null) {
            \exec(sprintf('kill %d', $this->pid));
        }

        $this->pid = null;
    }

    /**
     * Let the OS find an open port for you.
     *
     * @return int
     */
    private function findOpenPort(): int {
        $sock = \socket_create(AF_INET, SOCK_STREAM, 0);

        if (!\socket_bind($sock, $this->getHost(), 0)) {
            throw new CouldNotBindPortToAddressException();
        }

        \socket_getsockname($sock, $checkAddress, $checkPort);
        \socket_close($sock);

        if($checkPort > 0) {
            return $checkPort;
        }

        throw new NotFoundOpenPortException();
    }

    /**
     * @return string
     */
    private function getShellCommand(): string
    {
        $variables = [
            $this->php,
            $this->host,
            $this->port,
            $this->getServerScript(),
        ];

        $stdout = \tempnam(sys_get_temp_dir(), 'mockserv-stdout-');

        $cmd = \vsprintf('%s -S %s:%d %s', $variables);
        $cmd = \sprintf('%s > %s 2>&1 & echo $!', \escapeshellcmd($cmd), \escapeshellarg($stdout));

        return $cmd;
    }

    /**
     * @return string
     */
    private function getServerScript(): string
    {
        $parent = \dirname(__DIR__, 2);
        $script = \realpath($parent . self::SEP . 'server' .  self::SEP . 'TempWebServer.php');

        return \escapeshellarg($script);
    }

    /**
     * @return string
     */
    private function getCacheParams(): string
    {
        return \serialize($this->cache->toArray());
    }

    /**
     * @param string $path
     * @param ResponseInterface|null $response
     * @param bool $set
     *
     * @return bool
     */
    public function storeResponse(string $path, ?ResponseInterface $response, bool $set = true): bool
    {
        $responses = new ResponseContainer();
        $responses->addResponse($response ?? new Response());

        return $this->storeResponses($path, $responses, $set);
    }

    /**
     * @param string $path
     * @param ResponseContainer $responses
     * @param bool $set
     *
     * @return int
     */
    public function storeResponses(string $path, ResponseContainer $responses, bool $set = true): int
    {
        $cacheKey = $this->getCacheKey($path);

        if ($set === false) {
            $cache = $this->cache->get($cacheKey);

            if ($cache !== null) {
                $cacheResponses = \unserialize($cache, ['allowed_classes' => [
                    ResponseContainer::class,
                ]]);

                $cacheResponses->addResponsesFromContainer($responses);

                $responses = $cacheResponses;
            }
        }

        return $this->cache->set($cacheKey, \serialize($responses));
    }

    /**
     * @return bool
     */
    public function isRunning(): bool
    {
        if ($this->pid === null) {
            return false;
        }

        $cmd = \sprintf('ps %1$d | grep %1$d | wc -l', $this->pid);
        $result = \shell_exec($cmd);

        return (int) \trim($result) === 1;
    }

    /**
     * @return string
     */
    public function getServerHost(): string
    {
        return \implode(':', [$this->host, $this->port]);
    }

    /**
     * @return string
     */
    public function getServerUrl(): string
    {
        return \sprintf('http://%s/', $this->getServerHost());
    }

    /**
     * @param string $path
     *
     * @return string
     */
    public function getRequestUrl(string $path): string
    {
        return \rtrim($this->getServerUrl(), '/') . $path;
    }

    /**
     * @param string $path
     *
     * @return string
     */
    private function getCacheKey(string $path): string
    {
        return \md5($this->getRequestUrl($path));
    }

    /**
     * @return string
     */
    public function getHost(): string
    {
        return $this->host;
    }

    /**
     * Get the port the network server is to be ran on.
     *
     * @return int
     */
    public function getPort(): int
    {
        return $this->port;
    }

}