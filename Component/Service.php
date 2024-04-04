<?php

namespace Framework\Component;

/**
 * The Service class provides a base for implementing services in the application.
 *
 * These classes are responsible for registering services into the application container.
 *
 * @package Framework\Foundation
 */
class Service
{
    /**
     * Register any application services.
     *
     * This method is called by the application during the bootstrapping process.
     * Services should use this method to register any services they provide
     * into the application container.
     *
     * @param Application $app The application instance.
     * @return void
     */
    public function register(Application $app)
    {

    }
}
