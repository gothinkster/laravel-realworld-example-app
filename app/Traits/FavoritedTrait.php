<?php

namespace App\Traits;

use App\User;

trait FavoritedTrait
{
    public function getFavoritedAttribute()
    {
        if (! auth()->check()) {
            return false;
        }

        return $this->isFavoritedBy(auth()->user());
    }

    public function getFavoritesCountAttribute()
    {
        return $this->favorited()->count();
    }

    public function favorited()
    {
        return $this->belongsToMany(User::class, 'favorites', 'article_id', 'user_id')->withTimestamps();
    }

    public function isFavoritedBy(User $user)
    {
        return !! $this->favorited()->where('user_id', $user->id)->count();
    }
}