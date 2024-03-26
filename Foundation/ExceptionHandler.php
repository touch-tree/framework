<?php

namespace Framework\Foundation;

class ExceptionHandler
{
    public function __construct()
    {
        set_exception_handler([$this, ]);
    }

    public function render() {

    }
}