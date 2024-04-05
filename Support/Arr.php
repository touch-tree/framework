<?php

namespace Framework\Support;

/**
 * The Arr class provides utility methods for arrays.
 *
 * @package Framework\Support\Helpers
 */
class Arr
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
}