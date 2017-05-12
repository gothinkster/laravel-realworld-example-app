<?php

namespace App\RealWorld\Transformers;

class ArticleTransformer extends Transformer
{
    protected $resourceName = 'article';

    public function transform($data)
    {
        return [
            'slug'              => $data['slug'],
            'title'             => $data['title'],
            'description'       => $data['description'],
            'body'              => $data['body'],
            'tagList'           => $data['tagList'],
            'createdAt'         => $data['created_at']->toAtomString(),
            'updatedAt'         => $data['updated_at']->toAtomString(),
            'favorited'         => $data['favorited'],
            'favoritesCount'    => $data['favoritesCount'],
            'author' => [
                'username'  => $data['user']['username'],
                'bio'       => $data['user']['bio'],
                'image'     => $data['user']['image'],
                'following' => $data['user']['following'],
            ]
        ];
    }
}