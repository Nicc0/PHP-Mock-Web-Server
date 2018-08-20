<?php

namespace Nicc0\MockWebServer\Interfaces;

/**
 * Interface CacheTypeInterface
 *
 * @since 15.08.2018
 * @author Daniel Tęcza
 * @package Nicc0\MockWebServer\Interfaces
 */
interface CacheTypeInterface
{
    public function init(): bool;

    public function set(string $cacheKey, string $data, int $expire = 60): bool;

    public function get(string $cacheKey): ?string;

    public function toArray(): array;

    public function getClass(): string;
}