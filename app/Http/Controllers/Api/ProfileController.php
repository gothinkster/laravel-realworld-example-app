<?php

namespace App\Http\Controllers\Api;

use App\User;
use App\Transformers\ProfileTransformer;

class ProfileController extends ApiController
{
    public function __construct(ProfileTransformer $transformer)
    {
        $this->transformer = $transformer;

        $this->middleware('auth.api')->except('show');
    }

    public function show(User $user)
    {
        //
    }

    public function follow(User $user)
    {
        //
    }

    public function unFollow(User $user)
    {
        //
    }
}
