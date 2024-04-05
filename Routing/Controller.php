<?php

namespace Framework\Routing;

use Exception;
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
     * @return bool true if the validation passes, false otherwise.
     * @throws Exception If an unsupported validation rule is encountered.
     */
    public function validate(Request $request, array $rules): bool
    {
        return (new Validator($request->all(), $rules))->validate();
    }
}