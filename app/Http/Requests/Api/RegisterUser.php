<?php

namespace App\Http\Requests\Api;

class RegisterUser extends ApiRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'user.username' => 'required|max:50|alpha_num|unique:users,username',
            'user.email' => 'required|email|max:255|unique:users,email',
            'user.password' => 'required|min:6',
        ];
    }
}
