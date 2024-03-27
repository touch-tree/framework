<?php

namespace Framework\Foundation;

use Error;
use Exception;
use Framework\Foundation\Exception\HttpException;
use Framework\Http\Response;

/**
 * The ExceptionHandler class handles exceptions thrown within the framework.
 *
 * It provides a centralized place for exception handling logic.
 *
 * @package Framework\Foundation
 */
class ExceptionHandler
{
    /**
     * ExceptionHandler constructor.
     *
     * Initializes the exception handler by setting a custom exception handler.
     * When an exception occurs, it will be passed to the handle method of this class.
     */
    public function __construct()
    {
        set_exception_handler(fn($e) => $this->render($e));
    }

    private function render($e)
    {
        echo $this->handle($e)->send();
    }

    /**
     * Handle the exception.
     *
     * This method is called when an uncaught exception occurs within the framework.
     * It provides a centralized location to handle exceptions and implement custom logic.
     *
     * @param Exception|Error $exception The exception to handle
     */
    public function handle($exception): Response
    {
        if (is_a($exception, HttpException::class)) {
            return $exception->get_response();
        }

        return response(null, Response::HTTP_INTERNAL_SERVER_ERROR);
    }
}
