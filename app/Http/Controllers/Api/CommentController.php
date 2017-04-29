<?php

namespace App\Http\Controllers\Api;

use App\Article;
use App\Comment;
use App\Http\Requests\Api\CreateComment;
use App\Http\Requests\Api\DeleteComment;
use App\Transformers\CommentTransformer;

class CommentController extends ApiController
{
    public function __construct(CommentTransformer $transformer)
    {
        $this->transformer = $transformer;

        $this->middleware('auth.api')->except('index');
        $this->middleware('auth.api:optional')->only('index');
    }

    public function index(Article $article)
    {
        $comments = $article->comments()->get();

        return $this->respondWithTransformer($comments);
    }

    public function store(CreateComment $request, Article $article)
    {
        $comment = $article->comments()->create([
            'body' => $request->input('comment.body'),
            'user_id' => auth()->id(),
        ]);

        return $this->respondWithTransformer($comment);
    }

    public function destroy(DeleteComment $request, $article, Comment $comment)
    {
        $comment->delete();

        return $this->respondSuccess();
    }
}
