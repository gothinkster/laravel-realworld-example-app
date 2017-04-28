<?php

namespace App\Http\Controllers\Api;

use App\Article;
use App\Comment;
use App\Http\Requests\Api\CreateComment;
use App\Transformers\CommentTransformer;

class CommentController extends ApiController
{
    public function __construct(CommentTransformer $transformer)
    {
        $this->transformer = $transformer;

        $this->middleware('auth.api')->except('index');
    }

    public function index(Article $article)
    {
        //
    }

    public function store(CreateComment $request, Article $article)
    {
        //
    }

    public function destroy(Article $article, Comment $comment)
    {
        //
    }
}
