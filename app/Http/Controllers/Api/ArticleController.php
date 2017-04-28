<?php

namespace App\Http\Controllers\Api;

use App\Article;
use App\Http\Requests\Api\CreateArticle;
use App\Http\Requests\Api\UpdateArticle;
use App\Transformers\ArticleTransformer;

class ArticleController extends ApiController
{
    public function __construct(ArticleTransformer $transformer)
    {
        $this->transformer = $transformer;

        $this->middleware('auth.api')->except(['index', 'show']);
    }

    public function index()
    {
        //
    }

    public function store(CreateArticle $request)
    {
        //
    }

    public function show(Article $article)
    {
        //
    }

    public function update(UpdateArticle $request, Article $article)
    {
        //
    }

    public function destroy(Article $article)
    {
        //
    }
}
