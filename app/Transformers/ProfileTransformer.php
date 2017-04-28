<?php

namespace App\Transformers;

class ProfileTransformer extends Transformer
{
    public function item($data)
    {
        return [
            'profile' => [
                'username' => $data['username'],
                'bio' => $data['bio'],
                'image' => $data['image'],
                'following' => $data['following'],
            ]
        ];
    }
}