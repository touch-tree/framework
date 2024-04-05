<?php

namespace Framework\Http\Exceptions;

use Framework\Http\Response;

/**
 * Exception representing a 'Not Found' HTTP error.
 *
 * This exception should be thrown when a requested resource is not found.
 *
 * @package Framework\Http\Exceptions
 */
class NotFoundHttpException extends HttpException
{
    /**
     * Create a new NotFoundHttpException instance.
     *
     * @return void
     */
    public function __construct()
    {
        $view = view('errors.404');

        parent::__construct($view->render(), Response::HTTP_NOT_FOUND);
    }
}
