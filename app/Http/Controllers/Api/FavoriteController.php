<?php

namespace App\Http\Controllers\Api;

use App\Article;
use App\Transformers\ArticleTransformer;

class FavoriteController extends ApiController
{
    public function __construct(ArticleTransformer $transformer)
    {
        $this->transformer = $transformer;

        $this->middleware('auth.api');
    }

    public function add(Article $article)
    {
        $user = auth()->user();

        $user->favorite($article);

        return $this->respondWithTransformer($article);
    }

    public function remove(Article $article)
    {
        $user = auth()->user();

        $user->unFavorite($article);

        return $this->respondWithTransformer($article);
    }
}
