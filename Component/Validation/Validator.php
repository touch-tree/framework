<?php

namespace Framework\Component\Validation;

use Framework\Component\Exceptions\ValidationException;
use Framework\Component\ParameterBag;

/**
 * The Validator class provides a simple and extensible way to validate data based on specified rules.
 *
 * @package Framework\Component\Validation
 */
class Validator
{
    /**
     * The data to be validated.
     *
     * @var array
     */
    protected array $data;

    /**
     * The validation rules for each field.
     *
     * @var array
     */
    protected array $rules;

    /**
     * The array to store validation errors.
     *
     * @var ParameterBag
     */
    protected ParameterBag $errors;

    /**
     * Validator constructor.
     *
     * @param array $data The data to be validated.
     * @param array $rules An associative array where keys are parameter names and values are validation patterns (e.g. ['name' => 'required|string|max:255']).
     */
    public function __construct(array $data, array $rules)
    {
        $this->data = $data;
        $this->rules = $rules;
        $this->errors = new ParameterBag();
    }

    /**
     * Validates the data based on the specified rules.
     *
     * @throws bool false if validation fails otherwise true.
     */
    public function validate(): bool
    {
        foreach ($this->rules as $field => $rules) {
            array_map(fn($rule) => $this->apply_rule($field, $rule), explode('|', $rules));
        }

        return $this->errors->any();
    }

    /**
     * Applies a single validation rule to a field.
     *
     * @param string $field The field to validate.
     * @param string $rule The validation rule to apply.
     */
    protected function apply_rule(string $field, string $rule): void
    {
        if ($rule === 'required') {
            $this->is_required($field);
        }

        if ($rule === 'string') {
            $this->is_string($field);
        }

        if ($rule === 'alpha') {
            $this->is_alpha($field);
        }

        if ($rule === 'alpha_num') {
            $this->is_alpha_num($field);
        }

        if ($rule === 'numeric') {
            $this->is_numeric($field);
        }

        if ($rule === 'email') {
            $this->is_email($field);
        }
    }

    /**
     * Adds a validation error for a specific field and rule.
     *
     * @param string $field The field for which the validation error occurred.
     * @param string $rule The rule that was not satisfied.
     */
    public function add_error(string $field, string $rule): void
    {
        $this->errors->set($field, $this->errors->get($field, $rule));
    }

    /**
     * Retrieves the validation errors.
     *
     * @return ParameterBag The collection of validation errors.
     */
    public function errors(): ParameterBag
    {
        return $this->errors;
    }

    /**
     * Validates that the specified field is required and not empty.
     *
     * @param string $field The field to validate.
     */
    protected function is_required(string $field): void
    {
        if (empty($this->data[$field] ?? null)) {
            $this->add_error($field, 'This field is required.');
        }
    }

    /**
     * Validates that the specified field contains only letters (alphabetic characters).
     *
     * @param string $field The field to validate.
     */
    protected function is_alpha(string $field): void
    {
        if (!ctype_alpha($this->data[$field] ?? null)) {
            $this->add_error($field, 'This field must contain only alphabetic characters.');
        }
    }

    /**
     * Validates that the specified field contains only alphanumeric characters.
     *
     * @param string $field The field to validate.
     */
    protected function is_alpha_num(string $field): void
    {
        if (!ctype_alnum($this->data[$field] ?? null)) {
            $this->add_error($field, 'This field must contain only alphanumeric characters.');
        }
    }

    /**
     * Validates that the specified field is a string.
     *
     * @param string $field The field to validate.
     */
    protected function is_string(string $field): void
    {
        if (!is_string($this->data[$field] ?? null)) {
            $this->add_error($field, 'This field must be a string.');
        }
    }

    /**
     * Validates that the specified field is numeric.
     *
     * @param string $field The field to validate.
     */
    protected function is_numeric(string $field): void
    {
        if (!is_numeric($this->data[$field] ?? null)) {
            $this->add_error($field, 'This field must contain only numeric characters.');
        }
    }

    /**
     * Validates that the specified field is a valid email address.
     *
     * @param string $field The field to validate.
     */
    protected function is_email(string $field): void
    {
        if (!filter_var($this->data[$field] ?? null, FILTER_VALIDATE_EMAIL)) {
            $this->add_error($field, 'This field must contain a valid email.');
        }
    }
}
