<?php

namespace Framework\Pipeline\Interfaces;

use Closure;
use Framework\Http\Request;

/**
 * The Pipeable interface represents middleware components that can be used in a pipeline.
 *
 * @package Framework\Pipeline\Interfaces
 */
interface Pipeable
{
    /**
     * Handle an incoming request for a pipeline.
     *
     * @param Request $request The incoming HTTP request.
     * @param Closure $next The next middleware in the pipeline.
     * @return mixed The response returned by the next middleware.
     */
    public function handle(Request $request, Closure $next);
}