<?php

namespace Framework\Routing\Services;

use Framework\Component\Service;
use Framework\Http\Redirector;
use Framework\Http\Request;
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
        $this->container->bind(Router::class, function () {
            return new Router($this->container);
        });
    }

    /**
     * Register UrlGenerator.
     *
     * @return void
     */
    private function register_url_generator(): void
    {
        $this->container->bind(UrlGenerator::class, function () {
            return new UrlGenerator($this->container->get(Router::class)->routes(), $this->container->get(Request::class));
        });
    }

    /**
     * Register redirector.
     *
     * @return void
     */
    private function register_redirector(): void
    {
        $this->container->bind(Redirector::class, function () {
            return new Redirector($this->container->get(Session::class), $this->container->get(UrlGenerator::class));
        });
    }
}