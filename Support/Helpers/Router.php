<?php

namespace Framework\Support\Helpers;

use Framework\Routing\RouteCollection;
use Framework\Routing\Router as ApplicationRouter;

/**
 * Router helper.
 *
 * @package Framework\Support\Helpers
 * @see Router
 */
class Router extends Helper
{
    /**
     * Set the accessor for the facade.
     *
     * @return ApplicationRouter
     */
    protected static function accessor(): object
    {
        return get(ApplicationRouter::class);
    }

    /**
     * Register a GET route.
     *
     * @param string $uri The URI pattern for the route.
     * @param array|string $action An array representing the controller and method to be called for this route.
     * @return ApplicationRouter The Router instance.
     */
    public static function get(string $uri, $action): ApplicationRouter
    {
        return self::accessor()->get($uri, $action);
    }

    /**
     * Register a POST route.
     *
     * @param string $uri The URI pattern for the route.
     * @param array $action An array representing the controller and method to be called for this route.
     * @return ApplicationRouter The Router instance.
     */
    public static function post(string $uri, array $action): ApplicationRouter
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