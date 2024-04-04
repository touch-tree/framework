<?php

namespace Framework\Routing\Generator;

use Framework\Http\Request;
use Framework\Routing\RouteCollection;
use Framework\Support\Helpers\Str;

/**
 * The UrlGenerator class generates URLs for routes and resources within the application.
 *
 * This class provides methods to generate full URLs based on the current request and route configuration.
 *
 * @package Framework\Routing\Generator
 */
class UrlGenerator
{
    /**
     * Collection of registered routes.
     *
     * @var RouteCollection
     */
    protected RouteCollection $routes;

    /**
     * Request instance representing a HTTP request.
     *
     * @var Request
     */
    private Request $request;

    /**
     * UrlGenerator constructor.
     *
     * @param RouteCollection $routes The route collection.
     * @param Request $request The Request instance.
     */
    public function __construct(RouteCollection $routes, Request $request)
    {
        $this->routes = $routes;
        $this->request = $request;
    }

    /**
     * Get the full base URL for the application.
     *
     * @return string|null The full base URL for the application. Returns null if 'app.url' is not set.
     */
    public function full(): ?string
    {
        return config('app.url') ?: $this->request->root() . $this->get_relative_path();
    }

    /**
     * Get the relative path from the document root to the project directory.
     *
     * @return string The relative path from the document root to the project directory.
     */
    private function get_relative_path(): string
    {
        return str_replace(Str::finish($this->request->server('DOCUMENT_ROOT'), '/'), '', base_path());
    }

    /**
     * Generate an absolute URL for the given path with route parameters, optionally excluding the host.
     *
     * @param string $path The path to the resource.
     * @param array $parameters [optional] Route parameters to include in the URL.
     * @param bool $exclude_host [optional] Whether to exclude the host from the generated URL.
     * @return string The generated absolute URL.
     */
    public function to(string $path, array $parameters = [], bool $exclude_host = false): string
    {
        $route_path = $this->populate_route_parameters($path, $parameters);

        if ($exclude_host) {
            return $route_path;
        }

        return $this->full() . ltrim($route_path, '/');
    }

    /**
     * Populate route parameters in the given path with provided values.
     *
     * @param string $path The path containing route parameter placeholders.
     * @param array $parameters The route parameters and their corresponding values.
     * @return string The path with route parameters populated.
     */
    private function populate_route_parameters(string $path, array $parameters): string
    {
        foreach ($parameters as $key => $value) {
            $path = str_replace('{' . $key . '}', $value, $path);
        }

        return $path;
    }
}
