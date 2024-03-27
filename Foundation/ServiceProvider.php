<?php

namespace Framework\Foundation;

/**
 * Class ServiceProvider
 *
 * This class provides a base for implementing service providers in the application.
 * Service providers are responsible for registering services into the application container.
 *
 * @package Framework\Foundation
 */
class ServiceProvider
{
    /**
     * Register any application services.
     *
     * This method is called by the application during the bootstrapping process.
     * Service providers should use this method to register any services they provide
     * into the application container.
     *
     * @param Application $app The application instance.
     * @return void
     */
    public function register(Application $app)
    {

    }
}
