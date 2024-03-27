<?php

namespace Framework\Http\Exception;

use Framework\Http\Response;
use Throwable;

/**
 * Exception representing a 'not found' HTTP error.
 *
 * This exception should be thrown when a requested resource is not found.
 *
 * @package Framework\Http\Exception
 */
class NotFoundHttpException extends HttpException
{
    /**
     * Create a new NotFoundHttpException instance.
     *
     * @param string|null $message [optional] The error message (content).
     * @param int $code [optional] The HTTP response code (default: 404).
     * @param Throwable|null $previous [optional] The previous exception used for the exception chaining.
     */
    public function __construct(string $message = null, int $code = Response::HTTP_NOT_FOUND, Throwable $previous = null)
    {
        $default = view('errors.404')->render();

        parent::__construct($message ?? $default, $code, $previous);
    }
}
