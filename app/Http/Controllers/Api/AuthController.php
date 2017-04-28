<?php

namespace App\Http\Controllers\Api;

use Auth;
use App\User;
use App\Transformers\UserTransformer;
use App\Http\Requests\Api\LoginUser;
use App\Http\Requests\Api\RegisterUser;

class AuthController extends ApiController
{
    public function __construct(UserTransformer $transformer)
    {
        $this->transformer = $transformer;
    }

    public function login(LoginUser $request)
    {
        $credentials = $request->only('user.email', 'user.password');
        $credentials = $credentials['user'];

        if (! Auth::once($credentials)) {
            return $this->respondUnauthorized('Invalid credentials');
        }

        $user = $this->transformer->item(auth()->user());
        return $this->respond($user);
    }

    public function register(RegisterUser $request)
    {
        $user = User::create([
            'username' => $request->input('user.username'),
            'email' => $request->input('user.email'),
            'password' => $request->input('user.password'),
        ]);

        $user = $this->transformer->item($user);
        return $this->respondCreated($user);
    }
}
