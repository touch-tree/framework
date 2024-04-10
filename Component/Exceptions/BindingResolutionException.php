<?php

namespace Framework\Component\Exceptions;

use Exception;
use Framework\Http\Response;

/**
 * Exception thrown when there is an error resolving a binding in the service container.
 *
 * This exception should be thrown when an attempt to resolve a binding from the service container fails.
 *
 * @package Framework\Component\Exceptions
 */
class BindingResolutionException extends Exception
{
    /**
     * Create a new BindingResolutionException instance.
     *
     * @param string|null $message [optional] The exception message.
     * @param int $code [optional] The exception code.
     * @param Exception|null $previous [optional] The previous exception used for chaining.
     */
    public function __construct(string $message = null, int $code = Response::HTTP_INTERNAL_SERVER_ERROR, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}