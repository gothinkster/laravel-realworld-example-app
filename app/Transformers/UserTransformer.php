<?php

namespace App\Transformers;

class UserTransformer extends Transformer
{
    protected $resourceName = 'user';

    public function transform($data)
    {
        return [
            'id'        => $data['id'],
            'email'     => $data['email'],
            'token'     => $data['token'],
            'username'  => $data['username'],
            'bio'       => $data['bio'],
            'image'     => $data['image'],
        ];
    }
}