<?php

namespace App\Http\Requests\Api;

class DeleteComment extends ApiRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $comment = $this->route('comment');

        return $comment->user_id == auth()->id();
    }
}
