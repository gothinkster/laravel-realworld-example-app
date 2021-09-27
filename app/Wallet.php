<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Wallet extends Model
{
    public $fillable = ['user_id', 'balance'];

    const FIRST_BALANCE = 100000;

    public function scopeCreateForNewUser($q, $user)
    {
        return $q->create(['user_id' => $user->id, 'balance' => static::FIRST_BALANCE]);
    }

    public function deposit($amount)
    {
        $this->increment('balance', $amount);
    }

    public function withdraw($amount)
    {
        $this->decrement('balance', $amount);
    }

    /**
     * Relation to User
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
