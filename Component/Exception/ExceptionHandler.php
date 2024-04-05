<?php

namespace Framework\Component\Exception;

use Error;
use Exception;
use Framework\Http\Exception\HttpException;
use Framework\Http\Response;

/**
 * The ExceptionHandler class handles exceptions thrown within the framework providing a centralized place for exception handling logic.
 *
 * @package Framework\Component\Exception
 */
class ExceptionHandler
{
    /**
     * Handler constructor.
     *
     * Initializes the exception handler by setting a custom exception handler.
     * When an exception occurs, it will be passed to the handle method of this class.
     */
    public function __construct()
    {
        set_exception_handler(fn($exception) => $this->render($exception));
    }

    /**
     * Renders the exception.
     *
     * This method renders the exception by invoking the handle method and echoing its output.
     *
     * @param Exception|Error $exception The exception to render
     */
    private function render($exception)
    {
        echo $this->handle($exception)->send();
    }

    /**
     * Handle the exception.
     *
     * This method is called when an uncaught exception occurs within the framework.
     * It provides a centralized location to handle exceptions and implement custom logic.
     *
     * @param Exception|Error $exception The exception to handle
     * @return Response The response to send
     */
    public function handle($exception): Response
    {
        if (is_a($exception, HttpException::class)) {
            return $exception->get_response();
        }
        
        return response(null, Response::HTTP_INTERNAL_SERVER_ERROR);
    }
}