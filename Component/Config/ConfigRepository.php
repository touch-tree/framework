<?php

namespace Framework\Component\Config;

use Framework\Support\ArrayHelper;

/**
 * The Config class provides a simple configuration management system.
 *
 * This class allows setting, getting, and checking the existence of configuration values.
 *
 * @package Framework\Component
 */
class ConfigRepository
{
    /**
     * The array of configuration values.
     *
     * @var array
     */
    private static array $items = [];

    /**
     * Get the entire configuration array.
     *
     * @return array The array of configuration values.
     */
    public function all(): array
    {
        return self::$items;
    }

    /**
     * Set multiple configuration values at runtime.
     *
     * @param array $keys An associative array of configuration keys and their values.
     * @return void
     */
    public function set(array $keys): void
    {
        foreach ($keys as $key => $value) {
            ArrayHelper::set(self::$items, $key, $value);
        }
    }

    /**
     * Get the value of a configuration key.
     *
     * @param string $key The configuration key.
     * @param mixed $default [optional] The default value to return if the key does not exist.
     * @return mixed The value of the configuration key, or the default value if the key does not exist.
     */
    public function get(string $key, $default = null)
    {
        return ArrayHelper::get(self::$items, $key, $default);
    }

    /**
     * Check if a configuration key exists.
     *
     * @param string $key The configuration key.
     * @return bool true if the configuration key exists, false otherwise.
     */
    public function has(string $key): bool
    {
        return ArrayHelper::has(self::$items, $key);
    }
}
