<?php

namespace Framework\Component\Exceptions;

use Exception;

/**
 * Exception thrown when validation fails.
 *
 * @package Framework\Component\Exceptions
 */
class ValidationException extends Exception
{
    /**
     * The array of validation errors.
     *
     * @var array
     */
    protected array $errors;

    /**
     * Create a new validation exception instance.
     *
     * @param array $errors The array of validation errors.
     * @param string|null $message The error message.
     * @param int $code The error code.
     * @param Exception|null $previous The previous exception.
     */
    public function __construct(array $errors, string $message = null, int $code = 0, Exception $previous = null)
    {
        $this->errors = $errors;

        parent::__construct($message, $code, $previous);
    }

    /**
     * Get the validation errors.
     *
     * @return array The array of validation errors.
     */
    public function errors(): array
    {
        return $this->errors;
    }
}