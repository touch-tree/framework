<?php

namespace Framework\Support\Helpers;

use Framework\Component\Config\ConfigRepository;

/**
 * Config helper.
 *
 * @package Framework\Support\Helpers
 * @see ConfigRepository
 */
class Config extends Helper
{
    /**
     * Set the accessor for the facade.
     *
     * @return ConfigRepository
     */
    protected static function accessor(): object
    {
        return get(ConfigRepository::class);
    }

    /**
     * Get the entire configuration array.
     *
     * @return array The array of configuration values.
     */
    public static function all(): array
    {
        return self::accessor()->all();
    }

    /**
     * Set multiple configuration values at runtime.
     *
     * @param array $keys An associative array of configuration keys and their values.
     * @return void
     */
    public static function set(array $keys): void
    {
        self::accessor()->set($keys);
    }

    /**
     * Get the value of a configuration key.
     *
     * @param string $key The configuration key.
     * @param mixed $default [optional] The default value to return if the key does not exist.
     * @return mixed The value of the configuration key, or the default value if the key does not exist.
     */
    public static function get(string $key, $default = null)
    {
        return self::accessor()->get($key, $default);
    }

    /**
     * Check if a configuration key exists.
     *
     * @param string $key The configuration key.
     * @return bool true if the configuration key exists, false otherwise.
     */
    public static function has(string $key): bool
    {
        return self::accessor()->has($key);
    }
}