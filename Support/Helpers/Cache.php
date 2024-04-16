<?php

namespace Framework\Support\Helpers;

use Framework\Cache\CacheRepository;

/**
 * Cache helper.
 *
 * @package Framework\Support\Helpers
 * @see CacheRepository
 */
class Cache extends Helper
{
    /**
     * Set the accessor for the facade.
     *
     * @return CacheRepository
     */
    protected static function accessor(): object
    {
        return get(CacheRepository::class);
    }

    /**
     * Get an item from the cache.
     *
     * @param string $key The unique identifier for the cached item.
     * @param mixed $default The default value to return if the item is not found in the cache.
     * @return mixed|null The cached item value or the default value if the item is not found.
     */
    public static function get(string $key, $default = null)
    {
        return self::accessor()->get($key, $default);
    }

    /**
     * Store an item in the cache.
     *
     * @param string $key The unique identifier for the cached item.
     * @param mixed $value The value to be stored in the cache.
     * @param int $ttl The time-to-live for the cached item in seconds.
     * @return void
     */
    public static function put(string $key, $value, int $ttl): void
    {
        self::accessor()->put($key, $value, $ttl);
    }

    /**
     * Determine if an item exists in the cache.
     *
     * @param string $key The unique identifier for the cached item.
     * @return bool true if the item exists in the cache, false otherwise.
     */
    public static function has(string $key): bool
    {
        return self::accessor()->has($key);
    }

    /**
     * Remove an item from the cache.
     *
     * @param string $key The unique identifier for the cached item to be removed.
     * @return bool true if the item was successfully removed, false otherwise.
     */
    public static function forget(string $key): bool
    {
        return self::accessor()->forget($key);
    }

    /**
     * Increment the value of an item in the cache.
     *
     * @param string $key The unique identifier for the cached item.
     * @param int $value The value to increment the cached item by.
     * @return int|bool The new value of the cached item or false on failure.
     */
    public static function increment(string $key, int $value = 1)
    {
        return self::accessor()->increment($key, $value);
    }

    /**
     * Decrement the value of an item in the cache.
     *
     * @param string $key The unique identifier for the cached item.
     * @param int $value The value to decrement the cached item by.
     * @return int|bool The new value of the cached item or false on failure.
     */
    public static function decrement(string $key, int $value = 1)
    {
        return self::accessor()->decrement($key, $value);
    }

    /**
     * Clear all entries from the cache.
     *
     * @return void
     */
    public static function clear(): void
    {
        self::accessor()->clear();
    }
}
