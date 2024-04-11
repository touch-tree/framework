<?php

namespace Framework\Component;

use Closure;
use Exception;
use Framework\Component\Exceptions\BindingResolutionException;
use ReflectionClass;
use ReflectionParameter;

/**
 * The service container for managing and resolving instances of classes.
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
     * An array to store singleton instances.
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
     * Set the shared instance of the container.
     *
     * @param Container|null $container The container instance to be set as shared.
     * @return Container|null The shared container instance.
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
            static::$instance = new self();
        }

        return static::$instance;
    }

    /**
     * Get an instance of the specified class from the service container.
     *
     * @template T
     * @param class-string<T> $abstract The fully qualified class name to resolve.
     * @return T An instance of the specified class.
     */
    public function get(string $abstract)
    {
        $concrete = $this->get_concrete($abstract);

        if ($concrete instanceof Closure) {
            return $concrete();
        }

        if (is_object($concrete)) {
            return $concrete;
        }

        return $this->resolve($concrete);
    }

    /**
     * Get concrete implementation for the specified abstract.
     *
     * @param string $abstract The abstract class or interface name.
     * @return Closure|object|string The concrete implementation (either a Closure, an object, or a class name).
     */
    protected function get_concrete(string $abstract)
    {
        return self::$bindings[$abstract] ?? self::$instances[$abstract] ?? $abstract;
    }

    /**
     * Resolve an instance of the specified class using reflection.
     *
     * @param Closure|string $abstract The fully qualified class name or Closure.
     * @return object The resolved instance of the specified class.
     *
     * @throws BindingResolutionException
     */
    protected function resolve($abstract): object
    {
        try {
            $reflector = new ReflectionClass($abstract);
        } catch (Exception $exception) {
            throw new BindingResolutionException($abstract);
        }

        if ($constructor = $reflector->getConstructor()) {
            return $reflector->newInstanceArgs($this->resolve_dependencies($constructor->getParameters()));
        }

        return $reflector->newInstance();
    }

    /**
     * Resolve constructor dependencies.
     *
     * @param array<ReflectionParameter> $dependencies The constructor parameters.
     * @return array The resolved dependencies.
     */
    protected function resolve_dependencies(array $dependencies): array
    {
        $items = [];

        foreach ($dependencies as $dependency) {
            $type = $dependency->getType();

            if (!$type) {
                continue;
            }

            $items[] = $this->get($type->getName());
        }

        return $items;
    }

    /**
     * Bind an abstract class or interface to a concrete implementation.
     *
     * @param string $abstract The abstract class or interface.
     * @param Closure|string|object $concrete The closure, class name, or instance.
     * @return void
     */
    public function bind(string $abstract, $concrete): void
    {
        self::$bindings[$abstract] = $concrete;
    }

    /**
     * Bind an abstract class or interface to a singleton concrete implementation.
     *
     * @param string $abstract The abstract class or interface.
     * @param Closure|string|object $concrete The closure, class name, or instance.
     * @return void
     */
    public function singleton(string $abstract, $concrete): void
    {
        self::$instances[$abstract] = is_callable($concrete) ? $concrete() : $concrete;
    }

    /**
     * Determine if the given abstract type has been bound.
     *
     * @param string $abstract The abstract class or interface.
     * @return bool True if the abstract type is bound, false otherwise.
     */
    public static function is_bound(string $abstract): bool
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
    public function forget_binding(string $abstract): void
    {
        unset(self::$bindings[$abstract]);
    }
}
