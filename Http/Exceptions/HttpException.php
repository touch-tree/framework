<?php

namespace Framework\Http\Exceptions;

use Exception;
use Framework\Http\HeaderBag;
use Framework\Http\Response;

/**
 * The HttpException class represents an HTTP exception.
 *
 * @package Framework\Http\Exceptions
 */
class HttpException extends Exception
{
    /**
     * The HTTP status code of the exception.
     *
     * @var int
     */
    protected int $status_code;

    /**
     * The headers to be sent along with the exception response.
     *
     * @var HeaderBag
     */
    protected HeaderBag $headers;

    /**
     * HttpException constructor.
     *
     * @param string $message The exception message.
     * @param int $status_code [optional] The HTTP status code, 500 on default.
     * @param Exception|null $previous [optional] The previous exception for chaining, null on default.
     * @param HeaderBag|null $headers [optional] The headers to be sent along with the response, null on default.
     */
    public function __construct(string $message, int $status_code = 500, Exception $previous = null, HeaderBag $headers = null)
    {
        $this->status_code = $status_code;
        $this->headers = $headers ?: new HeaderBag();

        parent::__construct($message, $status_code, $previous);
    }

    /**
     * Get the HTTP status code of the exception.
     *
     * @return int
     */
    public function get_status_code(): int
    {
        return $this->status_code;
    }

    /**
     * Get the headers to be sent along with the exception response.
     *
     * @return HeaderBag
     */
    public function get_headers(): HeaderBag
    {
        return $this->headers;
    }

    /**
     * Get the response representing the exception.
     *
     * @return Response
     */
    public function get_response(): Response
    {
        return new Response($this->getMessage(), $this->get_status_code(), $this->get_headers());
    }
}