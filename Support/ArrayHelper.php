<?php

namespace Framework\Support;

use ArrayAccess;

/**
 * The ArrayHelper class provides utility methods for arrays.
 *
 * @package Framework\Support
 */
class ArrayHelper
{
    /**
     * Filter the array using the given callback and return the first result.
     *
     * @param array $array The array to filter.
     * @param callable|null $callback The callback function to filter the array.
     * @param mixed $default The default value if no matching element is found.
     * @return mixed The first matching element or the default value.
     */
    public static function first(array $array, ?callable $callback = null, $default = null)
    {
        if (is_null($callback)) {
            return empty($array) ? $default : reset($array);
        }

        foreach ($array as $key => $value) {
            if ($callback($value, $key)) {
                return $value;
            }
        }

        return $default;
    }

    /**
     * Filter the array using the given callback.
     *
     * @param array $array The array to filter.
     * @param callable $callback The callback function to filter the array.
     * @return array The filtered array.
     */
    public static function where(array $array, callable $callback): array
    {
        return array_filter($array, $callback, ARRAY_FILTER_USE_BOTH);
    }

    /**
     * Get an item from an array using 'dot' notation.
     *
     * @param array $array The array from which to retrieve the item.
     * @param string $key The key in dot notation.
     * @param mixed $default [optional] The default value to return if the key is not found.
     * @return mixed|null The value corresponding to the key, or the default value if the key is not found.
     */
    public static function get(array $array, string $key, $default = null)
    {
        if (!self::accessible($array)) {
            return $default;
        }

        if (self::exists($array, $key)) {
            return $array[$key];
        }

        foreach (self::explode_key($key) as $segment) {
            if (self::accessible($array) && self::exists($array, $segment)) {
                $array = $array[$segment];
                continue;
            }

            return $default;
        }

        return $array;
    }

    /**
     * Determine if the given key exists in the provided array.
     *
     * @param array $array The array to check.
     * @param string $key The key to check for.
     * @return bool true if the key exists in the array, false otherwise.
     */
    public static function exists(array $array, string $key): bool
    {
        return array_key_exists($key, $array);
    }

    /**
     * Determine if the given value is array accessible.
     *
     * @param mixed $value The value to check.
     * @return bool true if the value is array accessible, false otherwise.
     */
    protected static function accessible($value): bool
    {
        return is_array($value) || $value instanceof ArrayAccess;
    }

    /**
     * Set an array item to a given value using 'dot' notation.
     *
     * @param array $array The array to modify.
     * @param string $key The key in dot notation.
     * @param mixed $value The value to set.
     * @return $this
     */
    public static function set(array &$array, string $key, $value): ArrayHelper
    {
        foreach (self::explode_key($key) as $segment) {
            if (!isset($array[$segment]) || !is_array($array[$segment])) {
                $array[$segment] = [];
            }

            $array = &$array[$segment];
        }

        $array = $value;

        return new self();
    }

    /**
     * Check if the given key exists in the provided array.
     *
     * @param array $array The array to check.
     * @param string $key The key to check for.
     * @return bool true if the key exists in the array, false otherwise.
     */
    public static function has(array $array, string $key): bool
    {
        return !is_null(self::get($array, $key));
    }

    /**
     * Add an element to an array using 'dot' notation if it doesn't exist.
     *
     * @param array $array The array to modify.
     * @param string $key The key in dot notation.
     * @param mixed $value The value to add.
     * @return void
     */
    public static function add(array $array, string $key, $value): void
    {
        self::set($array, $key, $value);
    }

    /**
     * Push an item onto the end of an array using 'dot' notation.
     *
     * @param array $array The array to modify.
     * @param string $key The key in dot notation.
     * @param mixed $value The value to push.
     * @return void
     */
    public static function push(array &$array, string $key, $value): void
    {
        $overwrite = self::get($array, $key, []);

        if (!is_array($overwrite)) {
            throw new TypeError('Expected typeof array but received ' . gettype($array));
        }

        $overwrite[] = $value;

        self::set($array, $key, $overwrite);
    }

    /**
     * Get every item of the array except for a specified array of items.
     *
     * @param array $array The array to filter.
     * @param array|string $keys The keys to exclude.
     * @return array The filtered array.
     */
    public static function except(array $array, $keys): array
    {
        return array_diff_key($array, array_flip((array)$keys));
    }

    /**
     * Get a subset of the items from the given array.
     *
     * @param array $array The array to filter.
     * @param array|string $keys The keys to include.
     * @return array The filtered array.
     */
    public static function only(array $array, $keys): array
    {
        return array_intersect_key($array, array_flip((array)$keys));
    }

    /**
     * Pluck an array of values from an array.
     *
     * @param array $array The array to pluck from.
     * @param string|array $value The value(s) to pluck.
     * @param string|null $key [optional] The key to use as the array keys.
     * @return array The plucked array.
     */
    public static function pluck(array $array, $value, string $key = null): array
    {
        $results = [];

        foreach ($array as $item) {
            $item_value = is_array($item) ? self::get($item, $value) : null;

            if (is_null($key)) {
                $results[] = $item_value;
            } else {
                $item_key = is_array($item) ? self::get($item, $key) : null;
                $results[$item_key] = $item_value;
            }
        }

        return $results;
    }

    /**
     * Remove an array item from a given key using "dot" notation.
     *
     * @param array $array The array to modify.
     * @param string $key The key in dot notation.
     * @return array The modified array.
     */
    public static function forget(array &$array, string $key): array
    {
        $keys = self::explode_key($key);
        $last_key = array_pop($keys);

        foreach ($keys as $segment) {
            if (!isset($array[$segment]) || !is_array($array[$segment])) {
                return $array;
            }

            $array = &$array[$segment];
        }

        unset($array[$last_key]);

        return $array;
    }

    /**
     * Collapse an array of arrays into a single array.
     *
     * @param array $array The array to collapse.
     * @return array The collapsed array.
     */
    public static function collapse(array $array): array
    {
        return array_merge(...$array);
    }

    /**
     * Explode a key string into an array of segments using 'dot' notation.
     *
     * @param string $key The key to explode.
     * @return array The exploded key segments.
     */
    private static function explode_key(string $key): array
    {
        return explode('.', $key);
    }
}