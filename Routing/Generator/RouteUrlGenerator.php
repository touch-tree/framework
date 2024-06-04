<?php

namespace Framework\Routing\Generator;

use Framework\Support\Str;
use Framework\Support\UrlParser;

/**
 * The RouteUrlGenerator class generates URLs for routes and resources within the application.
 *
 * This class provides methods to generate full URLs based on the current request and route configuration.
 *
 * @package Framework\Routing\Generator
 */
class RouteUrlGenerator
{
    /**
     * UrlGenerator instance.
     *
     * @var UrlGenerator
     */
    protected UrlGenerator $url;

    /**
     * RouteUrlGenerator constructor.
     *
     * @param UrlGenerator $url The UrlGenerator instance.
     */
    public function __construct(UrlGenerator $url)
    {
        $this->url = $url;
    }

    /**
     * Generate a URL for the given path with query parameters.
     *
     * @param string $path The path to the resource.
     * @param array $parameters [optional] Query parameters to include in the URL.
     * @param bool $absolute [optional] Whether to generate an absolute URL (including scheme and host).
     * @return string The generated URL.
     */
    public function to(string $path, array $parameters = [], bool $absolute = true): string
    {
        $url = $this->url->full() . ltrim(Str::ends($path, '/'), '/');

        if ($parameters) {
            $url = rtrim($url, '/') . '?' . http_build_query($parameters);
        }

        return $absolute ? $url : $this->get_path($url);
    }

    /**
     * Get the path of an URL.
     *
     * @param mixed $url The URL path.
     * @param mixed $keep_query Keep the query.
     * @return string
     */
    public function get_path(string $url, bool $keep_query = true): string
    {
        $path = parse_url($url, PHP_URL_PATH);

        if ($keep_query && $query = parse_url($url, PHP_URL_QUERY)) {
            $path .= '?' . $query;
        }

        return $path;
    }

    /**
     * Populate route parameters in the given path with provided values.
     *
     * @param string $path The path containing route parameter placeholders.
     * @param array $parameters The route parameters and their corresponding values.
     * @return string The path with route parameters populated.
     */
    public function populate_route_parameters(string $path, array $parameters): string
    {
        foreach ($parameters as $key => $value) {
            $path = str_replace('{' . $key . '}', $value, $path);
        }

        return $path;
    }
}
