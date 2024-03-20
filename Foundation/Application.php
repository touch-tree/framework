<?php

namespace Framework\Foundation;

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
     * Service providers in this application.
     *
     * @var array<ServiceProvider>
     */
    private array $services = [];

    /**
     * Requested service providers.
     *
     * @var array<string>
     */
    private array $requested_services = [];

    /**
     * Application constructor.
     *
     * @param string|null $base_path The base path for this application.
     */
    public function __construct(string $base_path)
    {
        $this->set_base_path($base_path);

        static::set_instance($this);
    }

    /**
     * Set base path.
     *
     * @param string $base_path
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
     * @return void
     */
    public function bootstrap()
    {
        $this->bootstrap_services();
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    private function bootstrap_services(): void
    {
        $services = [];

        foreach ($this->get_requested_services() as $service) {
            $class = $this->get($service);

            if (!is_string($class) || !is_a($class, ServiceProvider::class)) {
                continue;
            }

            $class->register($this);

            $services[get_class($class)] = $class;
        }

        $this->services = $services;
    }

    /**
     * Get requested services.
     *
     * @return array<string>
     */
    private function get_requested_services(): array
    {
        return $this->requested_services;
    }

    /**
     * Set services.
     *
     * @param array $services
     * @return Application
     */
    public function set_services(array $services): Application
    {
        $this->requested_services = $services;

        return $this;
    }

    /**
     * Get every service provider in this application.
     *
     * @return array<ServiceProvider>
     */
    public function get_services(): array
    {
        return $this->services;
    }

    /**
     * Get the absolute path to the base directory of the application.
     *
     * @param string|null $path [optional] The relative path to append to the base path.
     * @return string The absolute path to the base directory of the application.
     */
    public function base_path(?string $path = null): string
    {
        return $this->base_path . DIRECTORY_SEPARATOR . ltrim($path);
    }
}
