<?php

namespace Framework\Routing\Generator;

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
     * Generate a URL for the given path with route parameters.
     *
     * @param string $path The path to the resource.
     * @param array $parameters [optional] Route parameters to include in the URL.
     * @param bool $absolute [optional] Whether to generate an absolute URL (including scheme and host).
     * @return string The generated URL.
     */
    public function to(string $path, array $parameters = [], bool $absolute = true): string
    {
        $url = new UrlParser($this->url->full() . ltrim($this->populate_route_parameters($path, $parameters), '/'));

        return $absolute ? $url->get_url() : $url->get_path();
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