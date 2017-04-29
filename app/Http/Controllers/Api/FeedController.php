<?php

namespace App\Http\Controllers\Api;

use App\Paginate\Paginator;
use App\Transformers\ArticleTransformer;

class FeedController extends ApiController
{
    public function __construct(ArticleTransformer $transformer)
    {
        $this->transformer = $transformer;

        $this->middleware('auth.api');
    }

    public function index()
    {
        $user = auth()->user();

        $articles = new Paginator($user->feed());

        return $this->respondWithPagination($articles);
    }
}
