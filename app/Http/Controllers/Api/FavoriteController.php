<?php

namespace App\Http\Controllers\Api;

use App\Article;
use App\RealWorld\Transformers\ArticleTransformer;

class FavoriteController extends ApiController
{
    /**
     * FavoriteController constructor.
     *
     * @param ArticleTransformer $transformer
     */
    public function __construct(ArticleTransformer $transformer)
    {
        $this->transformer = $transformer;

        $this->middleware('auth.api');
    }

    /**
     * Favorite the article given by its slug and return the article if successful.
     *
     * @param Article $article
     * @return \Illuminate\Http\JsonResponse
     */
    public function add(Article $article)
    {
        $user = auth()->user();

        $user->favorite($article);

        return $this->respondWithTransformer($article);
    }

    /**
     * Unfavorite the article given by its slug and return the article if successful.
     *
     * @param Article $article
     * @return \Illuminate\Http\JsonResponse
     */
    public function remove(Article $article)
    {
        $user = auth()->user();

        $user->unFavorite($article);

        return $this->respondWithTransformer($article);
    }
}
