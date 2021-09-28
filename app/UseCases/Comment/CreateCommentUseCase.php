<?php

namespace App\UseCases\Comment;

use App\Article;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Exceptions\HttpResponseException;

class CreateCommentUseCase
{
	const COMMENT_PRICE = 5000;

	private $user;

	private $request;

	private $article;

	public function perform(Request $request, Article $article)
	{
		$this->user = auth()->user();

		$this->request = $request;

		$this->article = $article;

		DB::beginTransaction();
		try {
			$this->createComment()
				 ->withdrawFromWallet();

			DB::commit();

			return $this->comment;
		} catch (\Exception $e) {
			DB::rollBack();

			throw new HttpResponseException(
				response(['message' => 'An error occurred while creating the comment.'])
			);
		}
	}

	private function createComment()
	{
		$request = $this->request;

		$this->comment = $this->article->comments()->create([
			'body' => $request->input('comment.body'),
            'user_id' => $this->user->id
		]);

		$this->user->newCommentAction();

		return $this;
	}

	private function withdrawFromWallet()
	{
		if ( ! $this->user->isCommentFree()) {
			$this->user->wallet->withdraw(static::COMMENT_PRICE);
			$this->newInvoice();
		}

		return $this;
	}

	private function newInvoice()
	{
		$this->user->invoices()->create(['description' => 'create new comment', 'price' => static::COMMENT_PRICE]);

		return $this;
	}
}
