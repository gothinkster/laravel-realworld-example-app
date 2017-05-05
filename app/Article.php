<?php

namespace App;

use App\Filters\Filterable;
use App\Traits\FavoritedTrait;
use App\Traits\HasSlug;
use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    use Filterable,
        FavoritedTrait,
        HasSlug;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title', 'description', 'body'
    ];

    /**
     * The relations to eager load on every query.
     *
     * @var array
     */
    protected $with = [
        'tags'
    ];

    /**
     * Get the list of tags attached to the article.
     *
     * @return array
     */
    public function getTagListAttribute()
    {
        return $this->tags->pluck('name')->toArray();
    }

    /**
     * Load all required relationships with only necessary content.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeLoadRelations($query)
    {
        return $query->with(['user.followers' => function ($query) {
                $query->where('follower_id', auth()->id());
            }])
            ->with(['favorited' => function ($query) {
                $query->where('user_id', auth()->id());
            }])
            ->withCount('favorited');
    }

    /**
     * Get the user that owns the article.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get all the comments for the article.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function comments()
    {
        return $this->hasMany(Comment::class)->latest();
    }

    /**
     * Get all the tags that belong to the article.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function tags()
    {
        return $this->belongsToMany(Tag::class);
    }

    /**
     * Get the key name for route model binding.
     *
     * @return string
     */
    public function getRouteKeyName()
    {
        return 'slug';
    }

    /**
     * Get the attribute name to slugify.
     *
     * @return string
     */
    public function getSlugSourceColumn()
    {
        return 'title';
    }
}
