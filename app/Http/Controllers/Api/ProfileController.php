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
        $this->middleware('auth.api:optional')->only('show');
    }

    public function show(User $user)
    {
        return $this->respondWithTransformer($user);
    }

    public function follow(User $user)
    {
        $authenticatedUser = auth()->user();

        $authenticatedUser->follow($user);

        return $this->respondWithTransformer($user);
    }

    public function unFollow(User $user)
    {
        $authenticatedUser = auth()->user();

        $authenticatedUser->unFollow($user);

        return $this->respondWithTransformer($user);
    }
}
