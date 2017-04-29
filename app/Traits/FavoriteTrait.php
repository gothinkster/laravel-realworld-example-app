<?php

namespace App\Traits;

use App\Article;

trait FavoriteTrait
{
    public function favorite(Article $article)
    {
        if (! $this->hasFavorited($article))
        {
            return $this->favorites()->attach($article);
        }
    }

    public function unFavorite(Article $article)
    {
        return $this->favorites()->detach($article);
    }

    public function favorites()
    {
        return $this->belongsToMany(Article::class, 'favorites', 'user_id', 'article_id')->withTimestamps();
    }

    public function hasFavorited(Article $article)
    {
        return !! $this->favorites()->where('article_id', $article->id)->count();
    }
}