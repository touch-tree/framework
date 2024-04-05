<?php

namespace Framework\Http;

/**
 * The Server class provides methods for accessing server variables and provides a fallback default value if the variable is not set.
 *
 * @package Framework\Http
 */
class Server
{
    /**
     * Get a server variable by key.
     *
     * @param string $key The key of the server variable to retrieve.
     * @param string|null $default The default value if the server variable is not set. Default is null.
     * @return mixed|string|null The value of the server variable or the default value.
     */
    public function get(string $key, ?string $default = null)
    {
        return $_SERVER[$key] ?? $default;
    }
}
