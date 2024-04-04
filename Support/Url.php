<?php

namespace Framework\Support;

use Framework\Http\Request;
use Framework\Routing\RouteCollection;
use Framework\Routing\Router;

/**
 * The URL class represents a utility for generating and manipulating URLs.
 *
 * This class provides methods for generating absolute URLs, retrieving the base URL for the application,
 * and getting the current URL. It supports handling of query parameters and provides options to exclude
 * the host from the generated URLs if needed.
 *
 * @package Framework\Support
 */
class Url
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
     * Url constructor.
     *
     * @param RouteCollection $routes The route collection.
     * @param Request $request The request instance.
     */
    public function __construct(RouteCollection $routes, Request $request)
    {
        $this->routes = $routes;
        $this->request = $request;
    }

    /**
     * Get instance.
     *
     * @return self
     */
    public static function get_instance(): self
    {
        static $instance;

        if (!isset($instance)) {
            $instance = new self(app(Router::class)->routes(), request());
        }

        return $instance;
    }

    /**
     * Generate a URL for the given route name.
     *
     * @param string $name The name of the route.
     * @param array $parameters [optional] Parameters to substitute into the route URI.
     * @param bool $absolute [optional] Whether to generate an absolute URL (including scheme and host).
     * @return string The generated URL.
     */
    public static function route(string $name, array $parameters = [], bool $absolute = true): string
    {
        $uri = self::get_instance()->routes->get($name, $parameters);

        if ($absolute) {
            $uri = self::get_instance()->request->root() . ltrim($uri, '/');
        }

        return $uri;
    }

    /**
     * Get the base URL for the application.
     *
     * @return string|null The base URL for the application. Returns relative path of document root to project directory if 'app.url' is not set.
     */
    public static function full(): ?string
    {
        return config('app.url') ?: self::get_instance()->request->root() . self::get_instance()->get_document_path();
    }

    /**
     * Get the relative path from the document root to the project directory.
     *
     * @return string The relative path from the document root to the project directory.
     */
    public function get_document_path(): string
    {
        $root = self::get_instance()->request->server('DOCUMENT_ROOT');
        $base = base_path();

        return str_replace(rtrim($root, '/') . '/', '', $base);
    }

    /**
     * Generate an absolute URL for the given path and parameters, optionally excluding the host.
     *
     * @param string $path The path to the resource.
     * @param array $parameters [optional] Parameters to append to the URL as query parameters.
     * @param bool $exclude_host [optional] Whether to exclude the host from the generated URL.
     * @return string The generated absolute URL.
     */
    public static function to(string $path, array $parameters = [], bool $exclude_host = false): string
    {
        $url = self::get_instance()::full() . ltrim($path, '/');

        if (!empty($parameters)) {
            $url .= '?' . http_build_query($parameters);
        }

        if ($exclude_host) {
            $parts = parse_url($url);
            $url = $parts['path'];

            if (isset($parts['query'])) {
                $url .= '?' . $parts['query'];
            }
        }

        return $url;
    }

    /**
     * Get the current URL.
     *
     * @return string The current URL.
     */
    public static function current(): string
    {
        return self::get_instance()->request->root() . ltrim(self::get_instance()->request->path(), '/');
    }
}

