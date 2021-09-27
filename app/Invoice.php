<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
	public $fillable = ['user_id', 'description', 'price'];

	public function user()
	{
		return $this->belongsTo(User::class);
	}
}
