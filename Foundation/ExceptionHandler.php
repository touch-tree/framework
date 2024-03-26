<?php

namespace Framework\Foundation;

use Exception;

class ExceptionHandler
{
    public function __construct()
    {
        set_exception_handler([$this, fn(Exception $e) => $this->render($e)]);
    }

    public function render()
    {

    }
}