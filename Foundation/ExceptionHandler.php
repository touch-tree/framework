<?php

namespace Framework\Foundation;

use Exception;

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
        set_exception_handler(fn(Exception $e) => $this->handle($e));
    }

    /**
     * Handle the exception.
     *
     * This method is called when an uncaught exception occurs within the framework.
     * It provides a centralized location to handle exceptions and implement custom logic.
     *
     * @param Exception $exception The exception to handle
     */
    public function handle(Exception $exception)
    {
        echo $exception->getMessage();
    }
}
