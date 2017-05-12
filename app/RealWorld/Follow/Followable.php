<?php

namespace App\RealWorld\Follow;

use App\User;

trait Followable
{
    /**
     * Check if the authenticated user is following this user.
     *
     * @return bool
     */
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

    /**
     * Follow the given user.
     *
     * @param User $user
     * @return mixed
     */
    public function follow(User $user)
    {
        if (! $this->isFollowing($user) && $this->id != $user->id)
        {
            return $this->following()->attach($user);
        }
    }

    /**
     * Unfollow the given user.
     *
     * @param User $user
     * @return mixed
     */
    public function unFollow(User $user)
    {
        return $this->following()->detach($user);
    }

    /**
     * Get all the users that this user is following.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function following()
    {
        return $this->belongsToMany(User::class, 'follows', 'follower_id', 'followed_id')->withTimestamps();
    }

    /**
     * Get all the users that are following this user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function followers()
    {
        return $this->belongsToMany(User::class, 'follows', 'followed_id', 'follower_id')->withTimestamps();
    }

    /**
     * Check if a given user is following this user.
     *
     * @param User $user
     * @return bool
     */
    public function isFollowing(User $user)
    {
        return !! $this->following()->where('followed_id', $user->id)->count();
    }

    /**
     * Check if a given user is being followed by this user.
     *
     * @param User $user
     * @return bool
     */
    public function isFollowedBy(User $user)
    {
        return !! $this->followers()->where('follower_id', $user->id)->count();
    }
}