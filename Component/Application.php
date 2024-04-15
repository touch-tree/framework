<?php

namespace Framework\Component;

use App\Http\Kernel;
use Framework\Component\Config\ConfigRepository;
use Framework\Component\Exceptions\ExceptionHandler;
use Framework\Http\Kernel as HttpKernel;
use Framework\Http\Request;
use Framework\Routing\Services\RoutingService;
use Framework\Support\ArrayHelper;
use Framework\Support\Collection;
use Framework\Support\Helpers\File;

/**
 * The Application class is responsible for bootstrapping the application and registering services.
 *
 * @package Framework\Component
 */
class Application extends Container
{
    /**
     * The base path of this application.
     *
     * @var string
     */
    private string $base_path;

    /**
     * Get loaded Services in this application.
     *
     * @var array<Service>
     */
    private array $loaded_services = [];

    /**
     * Services.
     *
     * @var array<string>
     */
    private array $services = [];

    /**
     * The path to the configuration directory.
     *
     * @var string
     */
    private string $config_path;

    /**
     * Application constructor.
     *
     * @param string|null $base_path The base path for this application.
     */
    public function __construct(string $base_path)
    {
        $this->set_base_path($base_path);
        $this->register_base_bindings();
        $this->register_base_services();

        static::set_instance($this);
    }

    /**
     * Get the path to the configuration directory.
     *
     * @return string
     */
    public function get_config_path(): string
    {
        return $this->config_path;
    }

    /**
     * Set the path to the configuration directory.
     *
     * @param string $path The path to the configuration directory.
     * @return void
     */
    public function set_config_path(string $path): void
    {
        $this->config_path = $path;
    }

    /**
     * Set base path.
     *
     * @param string $base_path The base path for this application.
     * @return Application
     */
    public function set_base_path(string $base_path): Application
    {
        $this->base_path = $base_path;

        return $this;
    }

    /**
     * Bootstrap the application.
     *
     * This method initiates the bootstrap process for the application, including the initialization of registered services.
     *
     * @return void
     */
    public function bootstrap(): void
    {
        $this->bootstrap_services();
        $this->load_configuration_files();
    }

    /**
     * Register base bindings.
     *
     * Bindings are references to classes registered in our service container.
     * They allow us to retrieve a global instance of a class using an identifier or namespace,
     * enabling us to reuse the same instance instead of creating a new one each time.
     *
     * @return void
     */
    private function register_base_bindings(): void
    {
        if (class_exists(Kernel::class) && is_a(Kernel::class, HttpKernel::class, true)) {
            $this->bind(HttpKernel::class, Kernel::class);
        } else {
            $this->bind(HttpKernel::class, HttpKernel::class);
        }

        $this->singleton(ExceptionHandler::class, function () {
            return new ExceptionHandler($this->get(Request::class));
        });
    }

    /**
     * Register base services.
     *
     * Here we register classes and components needed in this framework.
     * In this instance, the routing service is needed to register components related to our routing system.
     *
     * @return void
     */
    private function register_base_services(): void
    {
        $this->register(RoutingService::class);
    }

    /**
     * Load configuration files from the specified path and merge them into the configuration array.
     *
     * @return void
     */
    public function load_configuration_files(): void
    {
        foreach (File::files($this->get_config_path(), 'php') as $file) {
            $config = include $file->getPathname();

            if (!is_array($config)) {
                continue;
            }

            $this->get(ConfigRepository::class)->set(
                [
                    pathinfo($file->getFilename(), PATHINFO_FILENAME) => $config
                ]
            );
        }
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    private function bootstrap_services(): void
    {
        $this->load_services();
        $this->register_services();
    }

    /**
     * Load services into the application.
     *
     * @return void
     */
    private function load_services(): void
    {
        $services = new Collection($this->services);

        $services = $services
            ->each(fn(string $service) => $this->get($service))
            ->to_array();

        $this->loaded_services = $services;
    }

    /**
     * Get a service by class reference.
     *
     * @param Service|string $service
     * @return array|false
     */
    public function get_service($service)
    {
        $matches = ArrayHelper::where($this->get_services(), static fn($value) => $value === get_class($service));

        return reset($matches);
    }

    /**
     * Resolve a service provider instance from the class name.
     *
     * @param string $service
     * @return Service
     */
    public function resolve_service(string $service): Service
    {
        return (new $service())->set_container($this);
    }

    /**
     * Get a service by class reference.
     *
     * @param Service|string $service
     * @return array|Service|string
     */
    public function register($service)
    {
        if ($loaded = $this->get_service($service)) {
            return $loaded;
        }

        if (is_string($service)) {
            $service = $this->resolve_service($service);
        }

        $service->register();

        return $service;
    }

    /**
     * Register loaded services into the application.
     *
     * @return void
     */
    private function register_services(): void
    {
        foreach ($this->loaded_services as $service) {
            $service->register();
        }
    }

    /**
     * Set services.
     *
     * @param array $services The services to be set.
     * @return Application
     */
    public function set_services(array $services): Application
    {
        $this->services = $services;

        return $this;
    }

    /**
     * Get every component in this application.
     *
     * @return array<Service> The array of loaded Services.
     */
    public function get_services(): array
    {
        return $this->loaded_services;
    }

    /**
     * Get the absolute path to the base directory of the application.
     *
     * @param string|null $path The relative path to append to the base path.
     * @return string The absolute path to the base directory of the application.
     */
    public function base_path(?string $path = null): string
    {
        return normalize_path($this->base_path . DIRECTORY_SEPARATOR . ltrim($path));
    }
}
