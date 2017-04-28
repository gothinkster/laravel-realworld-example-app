<?php

namespace App\Http\Controllers\Api;

use App\Transformers\UserTransformer;
use App\Http\Requests\Api\UpdateUser;

class UserController extends ApiController
{
    public function __construct(UserTransformer $transformer)
    {
        $this->transformer = $transformer;

        $this->middleware('auth.api');
    }

    public function index()
    {
        $user = $this->transformer->item(auth()->user());

        return $this->respond($user);
    }

    public function update(UpdateUser $request)
    {
        $user = auth()->user();

        $user->update($request->get('user'));

        $user = $this->transformer->item($user);

        return $this->respond($user);
    }
}
