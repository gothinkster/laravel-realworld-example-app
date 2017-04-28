<?php

namespace App\Http\Requests\Api;

class LoginUser extends ApiRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'user.email' => 'required|email|max:255',
            'user.password' => 'required|min:6',
        ];
    }
}
