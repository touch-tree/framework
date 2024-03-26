<?php

namespace Framework\Foundation;

use App\Http\Kernel;
use Framework\Http\Kernel as HttpKernel;
use Framework\Routing\Router;
use Framework\Support\Collection;

/**
 * The Application class is responsible for bootstrapping the application and registering services.
 *
 * This class extends the Container class to provide dependency injection and service registration functionality.
 *
 * @package Framework\Foundation
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
     * Get loaded service providers in this application.
     *
     * @var array<ServiceProvider>
     */
    private array $loaded_services = [];

    /**
     * Service providers.
     *
     * @var array<string>
     */
    private array $services = [];

    /**
     * Application constructor.
     *
     * @param string|null $base_path The base path for this application.
     */
    public function __construct(string $base_path)
    {
        $this->set_base_path($base_path);
        $this->register_core_bindings();

        static::set_instance($this);
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
     * Register services.
     *
     * This method registers loaded services into the application.
     *
     * @return void
     */
    private function register_services(): void
    {
        foreach ($this->loaded_services as $service) {
            $service->register($this);
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
     * Get every service provider in this application.
     *
     * @return array<ServiceProvider> The array of loaded service providers.
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
        return $this->base_path . DIRECTORY_SEPARATOR . ltrim($path, DIRECTORY_SEPARATOR);
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

        $this->singleton(Router::class, function () {
            return new Router($this);
        });

        $this->singleton(ExceptionHandler::class, ExceptionHandler::class);
    }
}
