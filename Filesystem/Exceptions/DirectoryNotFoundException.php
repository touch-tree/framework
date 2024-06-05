<?php

namespace Framework\Filesystem\Exceptions;

use Exception;
use Framework\Http\Response;

/**
 * Exception thrown when a directory is not found.
 *
 * This exception should be thrown when attempting to access or manipulate a directory that does not exist.
 *
 * @package Framework\Filesystem\Exceptions
 */
class DirectoryNotFoundException extends Exception
{
    /**
     * Create a new DirectoryNotFoundException instance.
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