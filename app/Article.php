<?php

namespace App;

use App\Traits\FavoritedTrait;
use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    use FavoritedTrait;

    public function getTagListAttribute()
    {
        return $this->tags()->pluck('name')->toArray();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class);
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }
}
