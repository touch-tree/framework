<?php

namespace Framework\Routing;

use Framework\Component\Exceptions\ValidationException;
use Framework\Component\Validation\Validator;
use Framework\Http\Request;

/**
 * Base controller class.
 *
 * @package Framework\Routing
 */
class Controller
{
    /**
     * Validate the given request with the given rules.
     *
     * @param Request $request The request to be validated.
     * @param array $rules The validation rules to apply.
     * @return void
     *
     * @throws ValidationException If validation fails.
     */
    public function validate(Request $request, array $rules): void
    {
        $validator = new Validator($request->all(), $rules);
        $validator->validate();
    }
}