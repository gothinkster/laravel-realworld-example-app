<?php

namespace App\RealWorld\Favorite;

use App\Article;

trait HasFavorite
{
    /**
     * Favorite the given article.
     *
     * @param Article $article
     * @return mixed
     */
    public function favorite(Article $article)
    {
        if (! $this->hasFavorited($article))
        {
            return $this->favorites()->attach($article);
        }
    }

    /**
     * Unfavorite the given article.
     *
     * @param Article $article
     * @return mixed
     */
    public function unFavorite(Article $article)
    {
        return $this->favorites()->detach($article);
    }

    /**
     * Get the articles favorited by the user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function favorites()
    {
        return $this->belongsToMany(Article::class, 'favorites', 'user_id', 'article_id')->withTimestamps();
    }

    /**
     * Check if the user has favorited the given article.
     *
     * @param Article $article
     * @return bool
     */
    public function hasFavorited(Article $article)
    {
        return !! $this->favorites()->where('article_id', $article->id)->count();
    }
}