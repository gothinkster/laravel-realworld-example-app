<?php

namespace App\Transformers;

class ArticleTransformer extends Transformer
{
    protected $resourceName = 'article';

    public function transform($data)
    {
        return [
            'slug' => $data['slug'],
            'title' => $data['title'],
            'description' => $data['description'],
            'tagList' => $data['tagList'],
            'body' => $data['body'],
            'createdAt' => $data['created_at']->toIso8601String(),
            'updatedAt' => $data['updated_at']->toIso8601String(),
            'favorited' => $data['favorited'],
            'favoritesCount' => $data['favoritesCount'],
            'author' => [
                'username' => $data['user']['username'],
                'bio' => $data['user']['bio'],
                'image' => $data['user']['image'],
                'following' => $data['user']['following'],
            ]
        ];
    }
}