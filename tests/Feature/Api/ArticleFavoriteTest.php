<?php

namespace Tests\Feature\Api;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class ArticleFavoriteTest extends TestCase
{
    use DatabaseMigrations;

    protected $article;

    public function setUp()
    {
        parent::setUp();

        $this->article = $this->user->articles()->save(factory(\App\Article::class)->make());
    }

    /** @test */
    public function it_returns_the_article_favorite_properties_accordingly_when_favorited_and_unfavorited()
    {
        $response = $this->postJson("/api/articles/{$this->article->slug}/favorite", [], $this->headers);

        $response->assertStatus(200)
            ->assertJson([
                'article' => [
                    'favorited' => true,
                    'favoritesCount' => 1,
                ]
            ]);

        $response = $this->deleteJson("/api/articles/{$this->article->slug}/favorite", [], $this->headers);

        $response->assertStatus(200)
            ->assertJson([
                'article' => [
                    'favorited' => false,
                    'favoritesCount' => 0,
                ]
            ]);
    }

    /** @test */
    public function it_returns_the_correct_article_favorite_count_when_favorited_and_unfavorited()
    {
        $response = $this->getJson("/api/articles/{$this->article->slug}");

        $response->assertStatus(200)
            ->assertJson([
                'article' => [
                    'favoritesCount' => 0,
                ]
            ]);

        $this->user->favorite($this->article);

        $response = $this->getJson("/api/articles/{$this->article->slug}");

        $response->assertStatus(200)
            ->assertJson([
                'article' => [
                    'favoritesCount' => 1,
                ]
            ]);

        $response = $this->postJson("/api/articles/{$this->article->slug}/favorite", [], $this->headers);

        $response->assertStatus(200)
            ->assertJson([
                'article' => [
                    'favorited' => true,
                    'favoritesCount' => 2,
                ]
            ]);

        $response = $this->deleteJson("/api/articles/{$this->article->slug}/favorite", [], $this->headers);
        $response->assertStatus(200)
            ->assertJson([
                'article' => [
                    'favorited' => false,
                    'favoritesCount' => 1,
                ]
            ]);

        $this->user->unFavorite($this->article);

        $response = $this->getJson("/api/articles/{$this->article->slug}");

        $response->assertStatus(200)
            ->assertJson([
                'article' => [
                    'favoritesCount' => 0,
                ]
            ]);
    }

    /** @test */
    public function it_returns_an_unauthorized_error_when_trying_to_favorite_or_unfavorite_without_logging_in()
    {
        $response = $this->postJson("/api/articles/{$this->article->slug}/favorite");

        $response->assertStatus(401);

        $response = $this->deleteJson("/api/articles/{$this->article->slug}/favorite");

        $response->assertStatus(401);
    }
}
