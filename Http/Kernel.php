<?php

namespace Framework\Http;

use Framework\Component\Container;
use Framework\Component\View;
use Framework\Http\Exceptions\NotFoundHttpException;
use Framework\Pipeline\Pipeline;
use Framework\Routing\Route;
use Framework\Routing\Router;
use Framework\Session\Pipes\SessionPipe;

/**
 * The Kernel class is the central HTTP component of the application.
 *
 * This class is responsible for handling HTTP requests and preparing responses, including middleware processing and event emits.
 *
 * @package Framework\Http
 */
class Kernel
{
    /**
     * Array of global middleware to be applied to every request.
     *
     * @var array
     */
    protected array $pipes = [];

    /**
     * Array of global base middleware to be applied to every request.
     *
     * @var array
     */
    protected array $base_pipes = [
        SessionPipe::class
    ];

    /**
     * Array of route-specific middleware.
     *
     * @var array
     */
    protected array $route_pipes = [];

    /**
     * Router instance.
     *
     * @var Router
     */
    protected Router $router;

    /**
     * Container instance.
     *
     * @var Container
     */
    private Container $container;

    /**
     * Kernel constructor.
     *
     * @param Router $router The router instance.
     */
    public function __construct(Container $container, Router $router)
    {
        $this->container = $container;
        $this->router = $router;
    }

    /**
     * Handle an incoming HTTP request.
     *
     * @param Request $request The incoming HTTP request to be handled.
     * @return Response|null The response to the request, or null if no response is generated.
     */
    public function handle(Request $request): ?Response
    {
        $response = $this->prepare_response($request, $this->send_request_through_router($request));

        if (is_a($response, Response::class)) {
            echo $response->send();
        }

        $this->terminate($request);

        return $response;
    }

    /**
     * Send the request through the router and middleware pipeline.
     *
     * @param Request $request The incoming HTTP request.
     * @return mixed The response returned by the router and middleware pipeline.
     *
     * @throws NotFoundHttpException
     */
    public function send_request_through_router(Request $request)
    {
        $route = $this->router->find_route($request);

        if (is_null($route)) {
            throw new NotFoundHttpException();
        }

        $pipes = array_merge($this->pipes, $this->base_pipes, $this->get_pipes_for_route($route));

        return $this->container->get(Pipeline::class)
            ->send($request)
            ->through($pipes)
            ->then(fn($request) => $this->router->dispatch($request));
    }

    /**
     * Get the middleware for the route.
     *
     * @param mixed $route The route object.
     * @return array Array of middleware for the route.
     */
    protected function get_pipes_for_route(Route $route): array
    {
        $pipes = [];

        foreach ($route->pipes() as $pipe) {
            if (isset($this->route_pipes[$pipe])) {
                $pipes[] = $this->route_pipes[$pipe];
            }
        }

        return array_merge(...$pipes);
    }

    /**
     * Perform any final actions for the request lifecycle.
     *
     * @param Request $request The HTTP request object.
     * @return void
     */
    protected function terminate(Request $request): void
    {
        $request->session->forget(['flash', 'errors']);
    }

    /**
     * Prepare a response for the request.
     *
     * @param Request $request The HTTP request object.
     * @param View|RedirectResponse|JsonResponse|null $response The response to be prepared.
     * @return Response|null The prepared response object, or null if no valid response is generated.
     */
    private function prepare_response(Request $request, $response): ?Response
    {
        if ($response instanceof RedirectResponse) {
            $request->flash();
            return $response;
        }

        if ($response instanceof JsonResponse) {
            return $response;
        }

        if ($response instanceof View) {
            return response($response->render(), Response::HTTP_OK, $response->get_headers());
        }

        return null;
    }
}