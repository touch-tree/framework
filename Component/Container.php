<?php

namespace Framework\Component;

use Closure;
use Error;
use Exception;
use Framework\Component\Exceptions\BindingResolutionException;
use ReflectionClass;
use ReflectionMethod;
use ReflectionException;

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
     * @param array $parameters [optional] Parameters to override constructor parameters of the provided class or Closure.
     * @return T An instance of the specified class.
     */
    public function get(string $abstract, array $parameters = [])
    {
        $concrete = $this->get_concrete($abstract);

        if ($concrete instanceof Closure) {
            return $concrete(...$parameters);
        }

        return self::$instances[$abstract] = $this->resolve($concrete, $parameters);
    }

    /**
     * Get concrete.
     *
     * @param string $abstract
     * @return Closure|object|string
     */
    protected function get_concrete(string $abstract)
    {
        return $this->bindings[$abstract] ?? $abstract;
    }

    /**
     * Resolve an instance of the specified class using reflection.
     *
     * @param Closure|string $abstract The fully qualified class name or Closure.
     * @param array $parameters [optional] Parameters to override constructor parameters.
     * @return object The resolved instance of the specified class.
     *
     * @throws BindingResolutionException
     */
    private function resolve($abstract, array $parameters = []): ?object
    {
        try {
            $reflection_class = new ReflectionClass($abstract);

            if ($constructor = $reflection_class->getConstructor()) {
                return $reflection_class->newInstanceArgs(empty($parameters) ? $this->resolve_dependencies($constructor) : $parameters);
            }

            return $reflection_class->newInstance();
        } catch (Exception $exception) {
            throw new BindingResolutionException($abstract);
        }
    }

    /**
     * Resolve constructor dependencies.
     *
     * @param ReflectionMethod $constructor The constructor method.
     * @param array $parameters [optional] Parameters to override constructor parameters.
     * @return array|null The resolved dependencies.
     *
     * @throws BindingResolutionException
     */
    public function resolve_dependencies(ReflectionMethod $constructor, array $parameters = []): ?array
    {
        $dependencies = [];

        foreach ($constructor->getParameters() as $param) {
            $type = $param->getType();

            if ($type && $class_name = $type->getName()) {
                $dependencies[] = $this->get($class_name);
                continue;
            }

            if (!array_key_exists($param = $param->getName(), $parameters)) {
                throw new BindingResolutionException($param);
            }

            $dependencies[] = $parameters[$param];
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
        $this->bind($abstract, $concrete);

        self::$instances[$abstract] = $this->resolve($abstract);
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
