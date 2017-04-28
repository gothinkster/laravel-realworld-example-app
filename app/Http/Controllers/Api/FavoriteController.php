<?php

namespace App\Http\Controllers\Api;

use App\Article;

class FavoriteController extends ApiController
{
    public function __construct()
    {
        $this->middleware('auth.api');
    }

    public function add(Article $article)
    {
        //
    }

    public function remove(Article $article)
    {
        //
    }
}
