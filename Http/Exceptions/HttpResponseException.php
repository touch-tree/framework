<?php

namespace Framework\Http\Exceptions;

use Framework\Http\Response;
use RuntimeException;

class HttpResponseException extends RuntimeException
{
    /**
     * The underlying response instance.
     *
     * @var Response
     */
    protected Response $response;

    /**
     * Create a new HTTP response exception instance.
     *
     * @param  Response  $response
     * @return void
     */
    public function __construct(Response $response)
    {
        $this->response = $response;

        parent::__construct();
    }

    /**
     * Get the underlying response instance.
     *
     * @return Response
     */
    public function get_response(): Response
    {
        return $this->response;
    }
}