<?php

namespace App\UseCases\Article;

use App\Tag;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CreateArticleUseCase
{
	const ARTICLE_PRICE = 5000;

	private $user;

	private $request;

	private $article;

	public function perform(Request $request)
	{
		$this->user = auth()->user();

		$this->request = $request;

		DB::beginTransaction();
		try {
			$this->createArticle()
				 ->attachTags()
				 ->withdrawFromWallet()
				 ->newInvoice();

			DB::commit();

			return $this->article;
		} catch (\Exception $e) {
			DB::rollBack();

			throw new HttpResponseException(
				response(['message' => 'An error occurred while creating the article.'])
			);
		}
	}

	private function createArticle()
	{
		$request = $this->request;

		$this->article = $this->user->articles()->create([
			'title' => $request->input('article.title'),
			'description' => $request->input('article.description'),
			'body' => $request->input('article.body')
		]);

		return $this;
	}

	private function attachTags()
	{
		$inputTags = $this->request->input('article.tagList', []);

		$tags = array_map(function ($name) {
			return Tag::firstOrCreate(['name' => $name])->id;
		}, $inputTags);

		$this->article->tags()->attach($tags);

		return $this;
	}

	private function withdrawFromWallet()
	{
		$this->user->wallet->withdraw(static::ARTICLE_PRICE);

		return $this;
	}

	private function newInvoice()
	{
		$this->user->invoices()->create(['description' => 'create new article', 'price' => static::ARTICLE_PRICE]);

		return $this;
	}
}
