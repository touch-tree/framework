<?php

namespace Framework\Foundation;

use Error;
use Framework\Http\Kernel;
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
     * Service providers in this application.
     *
     * @var Collection<ServiceProvider>
     */
    private Collection $services;

    /**
     * Requested service providers.
     *
     * @var string[]
     */
    private array $requested_services;

    /**
     * Application constructor.
     *
     * @param string|null $base_path The base path for this application.
     */
    public function __construct(string $base_path)
    {
        $this->base_path = $base_path;
        $this->services = new Collection();

        static::set_instance($this);
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
        foreach ($this->get_requested_services() as $service) {
            $class = (string)$this->get($service);

            if (is_a($class, ServiceProvider::class)) {
                $class->register($this);

                $this->services->set(get_class($class), $class);
            }
        }
    }

    /**
     * Get every service provider in this application.
     *
     * @return Collection
     */
    public function get_services(): Collection
    {
        return $this->services;
    }

    /**
     * Get requested services.
     *
     * @return string[]
     */
    public function get_requested_services(): array
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
