<?php

namespace Framework\Support;

/**
 * The StringHelper class provides utility methods for string manipulation.
 *
 * @package Framework\Support
 */
class StringHelper
{
    /**
     * Append the given suffix to the string if it does not already end with it.
     *
     * @param string $value The original string.
     * @param string $suffix The suffix to be appended.
     * @return string The modified string.
     */
    public static function finish(string $value, string $suffix): string
    {
        return static::ends_with($value, $suffix) ? $value : $value . $suffix;
    }

    /**
     * Append the given prefix to the string if it does not already start with it.
     *
     * @param string $value The original string.
     * @param string $prefix The prefix to be appended.
     * @return string The modified string.
     */
    public static function start(string $value, string $prefix): string
    {
        return static::starts_with($value, $prefix) ? $value : $prefix . $value;
    }

    /**
     * Limit the number of characters in the string.
     *
     * @param string $value The original string.
     * @param int $limit The maximum number of characters.
     * @return string The truncated string.
     */
    public static function limit(string $value, int $limit = 100): string
    {
        if (mb_strlen($value) <= $limit) {
            return $value;
        }

        return rtrim(mb_substr($value, 0, $limit, 'UTF-8'));
    }

    /**
     * Wrap the given string with the given value.
     *
     * @param string $value The original string.
     * @param string $prefix The prefix to prepend.
     * @param string|null $suffix The suffix to append. If null, the prefix is used.
     * @return string The wrapped string.
     */
    public static function wrap(string $value, string $prefix, ?string $suffix = null): string
    {
        return $prefix . $value . ($suffix ?? $prefix);
    }

    /**
     * Determine if a given string ends with a given substring.
     *
     * @param string $haystack The string to search in.
     * @param string|string[] $needles The substring(s) to search for.
     * @return bool true if the string ends with any of the specified substrings, false otherwise.
     */
    public static function ends_with(string $haystack, $needles): bool
    {
        $needles = is_array($needles) ? $needles : [$needles];

        foreach ($needles as $needle) {
            if (!empty($needle) && substr($haystack, -strlen($needle)) === $needle) {
                return true;
            }
        }

        return false;
    }

    /**
     * Determine if a given string starts with a given substring.
     *
     * @param string $haystack The string to search in.
     * @param string|string[] $needles The substring(s) to search for.
     * @return bool true if the string starts with any of the specified substrings, false otherwise.
     */
    public static function starts_with(string $haystack, $needles): bool
    {
        $needles = is_array($needles) ? $needles : [$needles];

        foreach ($needles as $needle) {
            if (!empty($needle) && strncmp($haystack, $needle, strlen($needle)) === 0) {
                return true;
            }
        }

        return false;
    }

    /**
     * Append the given values to the string.
     *
     * @param string $value The original string.
     * @param array|string $append The value(s) to append.
     * @return string The modified string.
     */
    public static function append(string $value, $append): string
    {
        return $value . (is_array($append) ? implode($append) : $append);
    }
}
