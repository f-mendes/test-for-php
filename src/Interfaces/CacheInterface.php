<?php

declare(strict_types=1);

namespace App\Interfaces;

interface CacheInterface
{
    
    public function pull(string $key): array;

    public function set(string $key, array $value, int $ttl): bool;

    public function remove(string $key): bool;

    public function clear(): bool;

}