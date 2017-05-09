<?php

namespace Tests\Feature\Api;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class ArticleDeleteTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function it_returns_a_200_success_response_on_successfully_removing_the_article()
    {
        $article = $this->loggedInUser->articles()->save(factory(\App\Article::class)->make());

        $response = $this->deleteJson("/api/articles/{$article->slug}", [], $this->headers);

        $response->assertStatus(200);

        $response = $this->getJson("/api/articles/{$article->slug}");

        $response->assertStatus(404);
    }

    /** @test */
    public function it_returns_an_unauthorized_error_when_trying_to_remove_article_without_logging_in()
    {
        $article = $this->loggedInUser->articles()->save(factory(\App\Article::class)->make());

        $response = $this->deleteJson("/api/articles/{$article->slug}");

        $response->assertStatus(401);
    }

    /** @test */
    public function it_returns_a_forbidden_error_when_trying_to_remove_articles_by_others()
    {
        $article = $this->user->articles()->save(factory(\App\Article::class)->make());

        $response = $this->deleteJson("/api/articles/{$article->slug}", [], $this->headers);

        $response->assertStatus(403);
    }
}
