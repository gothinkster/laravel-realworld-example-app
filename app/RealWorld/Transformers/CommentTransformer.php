<?php

namespace App\RealWorld\Transformers;

class CommentTransformer extends Transformer
{
    protected $resourceName = 'comment';

    public function transform($data)
    {
        return [
            'id'        => $data['id'],
            'body'      => $data['body'],
            'createdAt' => $data['created_at']->toAtomString(),
            'updatedAt' => $data['updated_at']->toAtomString(),
            'author' => [
                'username'  => $data['user']['username'],
                'bio'       => $data['user']['bio'],
                'image'     => $data['user']['image'],
                'following' => $data['user']['following'],
            ]
        ];
    }
}