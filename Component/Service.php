<?php

namespace Framework\Component;

/**
 * The Service class provides a base for implementing services in the application.
 *
 * These classes are responsible for registering services into the application container.
 *
 * @package Framework\Component
 */
class Service
{
    /**
     * Container instance.
     *
     * @var Container
     */
    protected Container $app;

    /**
     * Service constructor.
     *
     * @param Container $app The Container instance.
     */
    public function __construct(Container $app)
    {
        $this->app = $app;
    }

    /**
     * Register any application services.
     *
     * This method is called by the application during the bootstrapping process.
     * Services should use this method to register any services they provide
     * into the application container.
     *
     * @return void
     */
    public function register()
    {

    }
}
