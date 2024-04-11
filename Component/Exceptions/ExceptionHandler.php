<?php

namespace Framework\Component\Exceptions;

use Error;
use Exception;
use Framework\Http\Exceptions\HttpException;
use Framework\Http\Request;
use Framework\Http\Response;
use Throwable;

/**
 * The ExceptionHandler class handles exceptions thrown within the framework providing a centralized place for exception handling logic.
 *
 * @package Framework\Component\Exceptions
 */
class ExceptionHandler
{
    /**
     * Handler constructor.
     *
     * Initializes the exception handler by setting a custom exception handler.
     * When an exception occurs, it will be passed to the handle method of this class.
     *
     * @param Request $request The HTTP request
     */
    public function __construct(Request $request)
    {
        set_exception_handler(fn($exception) => $this->render($exception, $request));
    }

    /**
     * Renders the exception.
     *
     * This method renders the exception by invoking the handle method and echoing its output.
     *
     * @param Exception|Error $exception The exception to render
     * @param Request $request The HTTP request
     */
    private function render(Throwable $exception, Request $request): void
    {
        echo $this->handle($exception, $request)->send();
    }

    /**
     * Handle the exception.
     *
     * This method is called when an uncaught exception occurs within the framework.
     * It provides a centralized location to handle exceptions and implement custom logic.
     *
     * @param Exception|Error $exception The exception to handle
     * @param Request $request The HTTP request
     * @return Response The response to send
     */
    public function handle(Throwable $exception, Request $request): Response
    {
        if (is_a($exception, HttpException::class)) {
            return $exception->get_response();
        }

        if ($exception instanceof ValidationException) {
            if ($request->expects_json()) {
                return response()->json(['errors' => $exception->errors()], Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            return redirect()->back()->with_errors($exception->errors());
        }

        return response(config('development_mode') ? $exception : null, Response::HTTP_INTERNAL_SERVER_ERROR);
    }
}