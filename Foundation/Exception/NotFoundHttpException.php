<?php

namespace Framework\Foundation\Exception;

use Framework\Http\Response;

class NotFoundHttpException extends HttpException
{
    public function __construct()
    {
        $content = view('errors.404')->render();

        parent::__construct($content, Response::HTTP_NOT_FOUND);
    }
}