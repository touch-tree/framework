<?php

namespace App\Http\Pipes;

use Closure;
use Framework\Http\Pipe;
use Framework\Http\Request;
use Framework\Session\Session;

class SessionPipe extends Pipe
{
    /**
     * Session instance.
     *
     * @var Session
     */
    private Session $session;

    /**
     * SessionPipe constructor.
     *
     * @param Session $session The session instance.
     */
    public function __construct(Session $session)
    {
        $this->session = $session;
    }

    /**
     * Handle an incoming request.
     *
     * @param Request $request The incoming request.
     * @param Closure $next The next middleware closure.
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $this->session->start();

        return $next($request);
    }
}