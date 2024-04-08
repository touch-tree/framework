<?php

namespace Framework\Support\Helpers;

/**
 * Abstract class for creating facades.
 *
 * This class provides a convenient base for creating facades in a framework.
 * A facade is a class that provides a static interface to objects that are
 * available in the application's service container.
 *
 * @package Framework\Support\Helpers
 */
abstract class Facade
{
    /**
     * Get the accessor class name.
     *
     * This method should be implemented by subclasses to return the fully
     * qualified class name of the accessor class that the facade represents.
     *
     * @return string The fully qualified class name of the accessor class.
     */
    abstract static protected function accessor(): string;

    /**
     * Get the accessor class instance from the application's service container.
     *
     * This method retrieves an instance of the accessor class from the
     * application's service container using the accessor class name returned
     * by the accessor() method.
     *
     * The accessor's dependencies will be resolved when retrieved from the service container.
     *
     * @return mixed The instance of the accessor class.
     */
    public static function get_accessor_class()
    {
        return get_service(static::accessor());
    }
}
