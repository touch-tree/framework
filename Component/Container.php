<?php

namespace Framework\Component;

use Closure;
use Error;
use Exception;
use ReflectionClass;
use ReflectionMethod;
use ReflectionException;

/**
 * The Dependency Injection Container for managing and resolving instances of classes.
 *
 * Represents a service container.
 *
 * @package Framework\Component
 */
class Container
{
    /**
     * The current globally available container instance (if any).
     *
     * @var static
     */
    protected static self $instance;

    /**
     * An array to store instances of resolved classes.
     *
     * @var array<string, object>
     */
    private static array $instances = [];

    /**
     * An array to store bindings of abstract classes or interfaces to concrete implementations.
     *
     * @var array<string, Closure|string|object>
     */
    private static array $bindings = [];

    /**
     * @param Container|null $container Set the shared instance of the container.
     * @return Container|null
     */
    public static function set_instance(Container $container = null): ?Container
    {
        return static::$instance = $container;
    }

    /**
     * Get the globally available instance of the container.
     *
     * @return static
     */
    public static function get_instance(): self
    {
        if (!isset(static::$instance)) {
            static::$instance = new Container();
        }

        return static::$instance;
    }

    /**
     * Get an instance of the specified class from the Container class.
     *
     * This function acts as a convenient entry point to retrieve instances of
     * classes from the application's Dependency Injection (DI) Container.
     *
     * @template T
     * @param class-string<T>|null $abstract [optional] The fully qualified class name to resolve.
     * @param array $parameters [optional] Parameters to override constructor parameters of the provided class or Closure.
     * @return T|Container|null An instance of the specified class, or null if the instance cannot be resolved.
     *
     * @see Container
     */
    public function get(string $abstract, array $parameters = []): ?object
    {
        if (isset(self::$bindings[$abstract])) {
            return self::$instances[$abstract] = $this->resolve(self::$bindings[$abstract], $parameters);
        }

        return $this->resolve($abstract, $parameters);
    }

    /**
     * Resolve an instance of the specified class using reflection.
     *
     * @param Closure|string $abstract The fully qualified class name or Closure.
     * @param array $parameters [optional] Parameters to override constructor parameters.
     * @return object|null|false The resolved instance of the specified class. null if the class does not exist. false if the class constructor is not public or if the class does not have a constructor and the $args parameter contains one or more parameters.
     */
    private function resolve($abstract, array $parameters = []): ?object
    {
        if ($abstract instanceof Closure) {
            return $abstract();
        }

        try {
            $reflection_class = new ReflectionClass($abstract);
        } catch (ReflectionException $exception) {
            return null;
        }

        try {
            if ($constructor = $reflection_class->getConstructor()) {
                return $reflection_class->newInstanceArgs(empty($parameters) ? $this->resolve_dependencies($constructor) : $parameters);
            }

            return $reflection_class->newInstance();
        } catch (Exception $exception) {
            return false;
        }
    }

    /**
     * Resolve constructor dependencies.
     *
     * @param ReflectionMethod $constructor The constructor method.
     * @param array $parameters [optional] Parameters to override constructor parameters.
     * @return array|null The resolved dependencies.
     */
    public function resolve_dependencies(ReflectionMethod $constructor, array $parameters = []): ?array
    {
        $dependencies = [];

        foreach ($constructor->getParameters() as $param) {
            $type = $param->getType();

            if ($type) {
                $class_name = $type->getName();

                if ($class_name) {
                    $dependencies[] = $this->get($class_name);
                    continue;
                }
            }

            if (array_key_exists($param->getName(), $parameters)) {
                $dependencies[] = $parameters[$param->getName()];
            } else {
                return null;
            }
        }

        return $dependencies;
    }

    /**
     * Bind an abstract class or interface to a concrete implementation.
     *
     * @param string $abstract The abstract class or interface.
     * @param Closure|string|object $concrete The closure, class name, or instance.
     * @return void
     */
    public function bind(string $abstract, $concrete)
    {
        self::$bindings[$abstract] = $concrete;
    }

    /**
     * Bind an abstract class or interface to a singleton concrete implementation.
     *
     * @param string $abstract The abstract class or interface.
     * @param Closure|string|object $concrete The closure, class name, or instance.
     * @return void
     *
     * @throws Error
     */
    public function singleton(string $abstract, $concrete)
    {
        $this->bind($abstract, $concrete);

        self::$instances[$abstract] = $this->resolve($abstract);
    }

    /**
     * Determine if the given abstract type has been bound.
     *
     * @param string $abstract The abstract class or interface.
     * @return bool True if the abstract type is bound, false otherwise.
     */
    public static function bound(string $abstract): bool
    {
        return isset(self::$bindings[$abstract]);
    }

    /**
     * Get the concrete implementation for a given abstract type.
     *
     * @param string $abstract The abstract class or interface.
     * @return Closure|string|object|null The closure, class name, or instance, or null if not bound.
     */
    public function get_binding(string $abstract)
    {
        return self::$bindings[$abstract] ?? null;
    }

    /**
     * Forget the concrete implementation for a given abstract type.
     *
     * @param string $abstract The abstract class or interface.
     * @return void
     */
    public function forget_binding(string $abstract)
    {
        unset(self::$bindings[$abstract]);
    }
}
