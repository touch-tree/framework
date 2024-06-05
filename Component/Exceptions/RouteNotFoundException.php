<?php

namespace Framework\Component\Exceptions;

use Exception;
use Framework\Http\Response;

/**
 * Exception thrown when route cannot be found.
 *
 * @package Framework\Component\Exceptions
 */
class RouteNotFoundException extends Exception
{
    /**
     * Create a new RouteNotFoundException instance.
     *
     * @param string|null $message The error message.
     * @param int $code The error code.
     * @param Exception|null $previous The previous exception.
     */
    public function __construct(string $message = null, int $code = Response::HTTP_INTERNAL_SERVER_ERROR, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}