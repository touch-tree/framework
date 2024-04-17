<?php

namespace Framework\Http;

use Framework\Component\Exceptions\ValidationException;
use Framework\Component\Validation\Validator;

/**
 * The FormRequest class represents a form request entity and extends the Base Request class.
 *
 * This class provides methods to handle and validate form data based on specified rules.
 * You can define custom validation rules within this class for specific use cases.
 *
 * @package Framework\Http
 */
class FormRequest extends Request
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Handle a failed validation attempt.
     *
     * @param Validator $validator The Validator instance.
     * @return void
     */
    public function failed(Validator $validator): void
    {
        throw new ValidationException($validator->errors()->all());
    }

    /**
     * Get the validation rules for the form request.
     *
     * @return array An array of validation rules.
     */
    public function rules(): array
    {
        return [];
    }

    /**
     * Validates multiple parameters based on specified rules and manages error handling.
     *
     * If no custom rules are provided, the default rules set in the class will be used.
     * The validation errors are stored in the session's 'errors' key, and the input data
     * is flashed to the session for convenient retrieval in subsequent requests.
     *
     * @param array $rules [optional] Set options to overwrite set options of custom rules.
     * @return Validator
     */
    public function validate(array $rules = []): Validator
    {
        $validator = new Validator($this->all(), $this->rules());

        if ($validator->validate()) {
            $this->failed($validator);
        }

        return $validator;
    }
}