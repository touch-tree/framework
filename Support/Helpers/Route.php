<?php

namespace Framework\Support\Helpers;

use Framework\Component\Exceptions\BindingResolutionException;
use Framework\Routing\RouteCollection;
use Framework\Routing\Router;

/**
 * Route facade.
 *
 * @package Framework\Support\Helpers
 * @see Router
 */
class Route extends Facade
{
    /**
     * Set the accessor for the facade.
     *
     * @return string
     */
    protected static function accessor(): string
    {
        return Router::class;
    }

    /**
     * Register a GET route.
     *
     * @param string $uri The URI pattern for the route.
     * @param array $action An array representing the controller and method to be called for this route.
     * @return Router The Router instance.
     *
     * @throws BindingResolutionException
     */
    public static function get(string $uri, array $action): Router
    {
        return self::get_accessor_class()->get($uri, $action);
    }

    /**
     * Register a POST route.
     *
     * @param string $uri The URI pattern for the route.
     * @param array $action An array representing the controller and method to be called for this route.
     * @return Router The Router instance.
     *
     * @throws BindingResolutionException
     */
    public static function post(string $uri, array $action): Router
    {
        return self::get_accessor_class()->post($uri, $action);
    }

    /**
     * Get the RouteCollection instance containing all registered routes.
     *
     * @return RouteCollection The RouteCollection instance.
     *
     * @throws BindingResolutionException
     */
    public function routes(): RouteCollection
    {
        return self::get_accessor_class()->routes();
    }
}