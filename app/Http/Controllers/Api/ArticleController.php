<?php

namespace App\Http\Controllers\Api;

use App\Filters\ArticleFilter;
use App\Tag;
use App\Article;
use App\Http\Requests\Api\CreateArticle;
use App\Http\Requests\Api\UpdateArticle;
use App\Http\Requests\Api\DeleteArticle;
use App\Transformers\ArticleTransformer;

class ArticleController extends ApiController
{
    public function __construct(ArticleTransformer $transformer)
    {
        $this->transformer = $transformer;

        $this->middleware('auth.api')->except(['index', 'show']);
        $this->middleware('auth.api:optional')->only(['index', 'show']);
    }

    public function index(ArticleFilter $filter)
    {
        // WIP
        $articles = Article::latest()->filter($filter)->get();
        
        return $this->respondWithTransformer($articles);
    }

    public function store(CreateArticle $request)
    {
        $user = auth()->user();

        $article = $user->articles()->create([
            'title' => $request->input('article.title'),
            'description' => $request->input('article.description'),
            'body' => $request->input('article.body'),
        ]);

        $inputTags = $request->input('article.tagList');

        if ($inputTags && ! empty($inputTags)) {

            $tags = array_map(function($name) {
                return Tag::firstOrCreate(['name' => $name])->id;
            }, $inputTags);

            $article->tags()->attach($tags);
        }

        return $this->respondWithTransformer($article);
    }

    public function show(Article $article)
    {
        $article->load('user');
        
        return $this->respondWithTransformer($article);
    }

    public function update(UpdateArticle $request, Article $article)
    {
        if ($request->has('article')) {
            $article->update($request->get('article'));
        }

        return $this->respondWithTransformer($article);
    }

    public function destroy(DeleteArticle $request, Article $article)
    {
        $article->delete();

        return $this->respondSuccess();
    }
}
