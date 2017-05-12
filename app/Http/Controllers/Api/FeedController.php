<?php

namespace App\Http\Controllers\Api;

use App\RealWorld\Paginate\Paginate;
use App\RealWorld\Transformers\ArticleTransformer;

class FeedController extends ApiController
{
    /**
     * FeedController constructor.
     *
     * @param ArticleTransformer $transformer
     */
    public function __construct(ArticleTransformer $transformer)
    {
        $this->transformer = $transformer;

        $this->middleware('auth.api');
    }

    /**
     * Get all the articles of users that are followed by the authenticated user.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $user = auth()->user();

        $articles = new Paginate($user->feed());

        return $this->respondWithPagination($articles);
    }
}
