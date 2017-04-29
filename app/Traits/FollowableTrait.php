<?php

namespace App\Traits;

use App\User;

trait FollowableTrait
{
    public function getFollowingAttribute()
    {
        if (! auth()->check()) {
            return false;
        }

        if (! $this->relationLoaded('followers')) {
            $this->load(['followers' => function ($query) {
                $query->where('follower_id', auth()->id());
            }]);
        }

        $followers = $this->getRelation('followers');

        if (! empty($followers) && $followers->contains('id', auth()->id())) {
            return true;
        }

        return false;
    }

    public function follow(User $user)
    {
        if (! $this->isFollowing($user) && $this->id != $user->id)
        {
            return $this->following()->attach($user);
        }
    }

    public function unFollow(User $user)
    {
        return $this->following()->detach($user);
    }

    public function following()
    {
        return $this->belongsToMany(User::class, 'follows', 'follower_id', 'followed_id')->withTimestamps();
    }

    public function followers()
    {
        return $this->belongsToMany(User::class, 'follows', 'followed_id', 'follower_id')->withTimestamps();
    }

    public function isFollowing(User $user)
    {
        return !! $this->following()->where('followed_id', $user->id)->count();
    }

    public function isFollowedBy(User $user)
    {
        return !! $this->followers()->where('follower_id', $user->id)->count();
    }
}