<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserAction extends Model
{
    protected $fillables = ['user_id', 'comment_count'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
