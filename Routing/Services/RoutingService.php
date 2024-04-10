<?php

namespace Framework\Routing\Services;

use Framework\Component\Service;
use Framework\Http\Redirector;
use Framework\Routing\Generator\UrlGenerator;
use Framework\Routing\Router;
use Framework\Session\Session;

class RoutingService extends Service
{
    /**
     * Register the services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->register_router();
        $this->register_url_generator();
        $this->register_redirector();
    }

    /**
     * Register the application's router
     *
     * @return void
     */
    private function register_router(): void
    {
        $this->app->singleton(Router::class, function () {
            return new Router($this->app);
        });
    }

    /**
     * Register UrlGenerator.
     *
     * @return void
     */
    private function register_url_generator(): void
    {
        $this->app->singleton(UrlGenerator::class, function () {
            return new UrlGenerator($this->app->get(Router::class)->routes(), request());
        });
    }

    /**
     * Register redirector.
     *
     * @return void
     */
    private function register_redirector(): void
    {
        $this->app->singleton(Redirector::class, function () {
            return new Redirector($this->app->get(Session::class), $this->app->get(UrlGenerator::class));
        });
    }
}