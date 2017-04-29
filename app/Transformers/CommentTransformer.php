<?php

namespace App\Transformers;

class CommentTransformer extends Transformer
{
    protected $resourceName = 'comment';

    public function transform($data)
    {
        return [
            'id' => $data['id'],
            'body' => $data['body'],
            'createdAt' => $data['created_at']->toIso8601String(),
            'updatedAt' => $data['updated_at']->toIso8601String(),
            'author' => [
                'username' => $data['user']['username'],
                'bio' => $data['user']['bio'],
                'image' => $data['user']['image'],
                'following' => $data['user']['following'],
            ]
        ];
    }
}