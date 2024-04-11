<?php

namespace Framework\Support\Helpers;

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
     * @return Router
     */
    protected static function accessor(): object
    {
        return get_service(Router::class);
    }

    /**
     * Register a GET route.
     *
     * @param string $uri The URI pattern for the route.
     * @param array $action An array representing the controller and method to be called for this route.
     * @return Router The Router instance.
     */
    public static function get(string $uri, array $action): Router
    {
        return self::accessor()->get($uri, $action);
    }

    /**
     * Register a POST route.
     *
     * @param string $uri The URI pattern for the route.
     * @param array $action An array representing the controller and method to be called for this route.
     * @return Router The Router instance.
     */
    public static function post(string $uri, array $action): Router
    {
        return self::accessor()->post($uri, $action);
    }

    /**
     * Get the RouteCollection instance containing all registered routes.
     *
     * @return RouteCollection The RouteCollection instance.
     */
    public function routes(): RouteCollection
    {
        return self::accessor()->routes();
    }
}