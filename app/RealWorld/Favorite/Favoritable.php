<?php

namespace App\RealWorld\Favorite;

use App\User;

trait Favoritable
{
    /**
     * Check if the authenticated user has favorited the article.
     * We make use of lazy loading if the relationship is not already loaded.
     *
     * @return bool
     */
    public function getFavoritedAttribute()
    {
        if (! auth()->check()) {
            return false;
        }

        if (! $this->relationLoaded('favorited')) {
            $this->load(['favorited' => function ($query) {
                $query->where('user_id', auth()->id());
            }]);
        }

        $favorited = $this->getRelation('favorited');

        if (! empty($favorited) && $favorited->contains('id', auth()->id())) {
            return true;
        }

        return false;
    }

    /**
     * Get the favorites count of the article.
     *
     * @return integer
     */
    public function getFavoritesCountAttribute()
    {
        if (array_key_exists('favorited_count', $this->getAttributes())) {
            return $this->favorited_count;
        }

        return $this->favorited()->count();
    }

    /**
     * Get the users that favorited the article.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function favorited()
    {
        return $this->belongsToMany(User::class, 'favorites', 'article_id', 'user_id')->withTimestamps();
    }

    /**
     * Check if the article is favorited by the given user.
     *
     * @param User $user
     * @return bool
     */
    public function isFavoritedBy(User $user)
    {
        return !! $this->favorited()->where('user_id', $user->id)->count();
    }
}