<?php

namespace Framework\Component;

use App\Http\Kernel;
use Framework\Component\Exceptions\ExceptionHandler;
use Framework\Http\Kernel as HttpKernel;
use Framework\Routing\Services\RoutingService;
use Framework\Support\Arr;
use Framework\Support\Collection;
use Framework\Support\Helpers\File;

/**
 * The Application class is responsible for bootstrapping the application and registering services.
 *
 * This class extends the Container class to provide dependency injection and service registration functionality.
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
        $this->register_core_bindings();
        $this->register_core_services();

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
    public function set_config_path(string $path)
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
    public function bootstrap()
    {
        $this->bootstrap_services();
        $this->load_configuration_files();
    }

    /**
     * Register core bindings.
     *
     * This method registers core bindings for the application.
     *
     * @return void
     */
    private function register_core_bindings()
    {
        $this->singleton(HttpKernel::class, Kernel::class);
        $this->singleton(ExceptionHandler::class, ExceptionHandler::class);
    }

    /**
     * Register core services.
     *
     * This method registers core services for the application.
     *
     * @return void
     */
    private function register_core_services()
    {
        $this->register(RoutingService::class);
    }

    /**
     * Load configuration files from the specified path and merge them into the configuration array.
     *
     * @return void
     */
    public function load_configuration_files()
    {
        foreach (File::get($this->get_config_path(), 'php') as $file) {
            $config = include $file;

            if (!is_array($config)) {
                continue;
            }

            $this->get(Config::class)->set($config);
        }
    }

    /**
     * Bootstrap services.
     *
     * This method iterates over the requested services and initializes them by calling their registration methods.
     *
     * @return void
     */
    private function bootstrap_services()
    {
        $this->load_services();
        $this->register_services();
    }

    /**
     * Load services.
     *
     * This method loads services into the application.
     *
     * @return void
     */
    private function load_services()
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
        $matches = Arr::where($this->get_services(), fn($value) => $value === get_class($service));

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
        return new $service($this);
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
     * Register services.
     *
     * This method registers loaded services into the application.
     *
     * @return void
     */
    private function register_services()
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
