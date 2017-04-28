<?php

namespace App\Transformers;

class UserTransformer extends Transformer
{
    public function item($data)
    {
        return [
            'user' => [
                'id' => $data['id'],
                'email' => $data['email'],
                'token' => $data['token'],
                'username' => $data['username'],
                'bio' => $data['bio'],
                'image' => $data['image'],
            ]
        ];
    }
}