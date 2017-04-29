<?php

namespace App\Http\Requests\Api;

class UpdateUser extends ApiRequest
{
    /**
     * Get data to be validated from the request.
     *
     * @return array
     */
    protected function validationData()
    {
        return $this->get('user') ?: [];
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'username' => 'sometimes|max:50|alpha_num|unique:users,username,' . $this->user()->id,
            'email' => 'sometimes|email|max:255|unique:users,email,' . $this->user()->id,
            'password' => 'sometimes|min:6',
            'bio' => 'sometimes|nullable|max:255',
            'image' => 'sometimes|nullable|url',
        ];
    }
}
