<?php

namespace Framework\Foundation;

/**
 * This Component class provides a base for implementing services in the application.
 * Components are responsible for registering services into the application container.
 *
 * @package Framework\Foundation
 */
class Component
{
    /**
     * Register any application services.
     *
     * This method is called by the application during the bootstrapping process.
     * Components should use this method to register any services they provide
     * into the application container.
     *
     * @param Application $app The application instance.
     * @return void
     */
    public function register(Application $app)
    {

    }
}
