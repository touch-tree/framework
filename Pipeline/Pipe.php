<?php

namespace Framework\Pipeline;

use Closure;
use Framework\Http\Request;

/**
 * The Pipe class represents a middleware component that can intercept and process HTTP requests in a pipeline.
 *
 * Pipe classes can perform tasks such as authentication, logging, or modifying request/response objects.
 *
 * @package Framework\Pipeline
 */
class Pipe
{
    /**
     * Handle an incoming request for a pipeline.
     *
     * @param Request $request The incoming HTTP request.
     * @param Closure $next The next middleware in the pipeline.
     * @return mixed The response returned by the next middleware.
     */
    public function handle(Request $request, Closure $next)
    {
        return $next($request);
    }
}
