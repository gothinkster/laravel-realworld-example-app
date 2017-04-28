<?php

namespace App\Http\Requests\Api;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class ApiRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Format the errors from the given Validator instance.
     *
     * @param Validator|\Illuminate\Contracts\Validation\Validator $validator
     * @return array
     */
    protected function formatErrors(Validator $validator)
    {
        return [
            "errors" => $validator->getMessageBag()->toArray()
        ];
    }
}
