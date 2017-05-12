<?php

namespace App\Http\Controllers\Api;

use App\User;
use App\RealWorld\Transformers\ProfileTransformer;

class ProfileController extends ApiController
{
    /**
     * ProfileController constructor.
     *
     * @param ProfileTransformer $transformer
     */
    public function __construct(ProfileTransformer $transformer)
    {
        $this->transformer = $transformer;

        $this->middleware('auth.api')->except('show');
        $this->middleware('auth.api:optional')->only('show');
    }

    /**
     * Get the profile of the user given by their username
     *
     * @param User $user
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(User $user)
    {
        return $this->respondWithTransformer($user);
    }

    /**
     * Follow the user given by their username and return the user if successful.
     *
     * @param User $user
     * @return \Illuminate\Http\JsonResponse
     */
    public function follow(User $user)
    {
        $authenticatedUser = auth()->user();

        $authenticatedUser->follow($user);

        return $this->respondWithTransformer($user);
    }

    /**
     * Unfollow the user given by their username and return the user if successful.
     *
     * @param User $user
     * @return \Illuminate\Http\JsonResponse
     */
    public function unFollow(User $user)
    {
        $authenticatedUser = auth()->user();

        $authenticatedUser->unFollow($user);

        return $this->respondWithTransformer($user);
    }
}
