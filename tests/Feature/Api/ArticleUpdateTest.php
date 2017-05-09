<?php

namespace Tests\Feature\Api;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class ArticleUpdateTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function it_returns_the_updated_article_on_successfully_updating_the_article()
    {
        $article = $this->loggedInUser->articles()->save(factory(\App\Article::class)->make());

        $data = [
            'article' => [
                'title' => 'new title',
                'description' => 'new description',
                'body' => 'new body with random text',
            ]
        ];

        $response = $this->putJson("/api/articles/{$article->slug}", $data, $this->headers);

        $response->assertStatus(200)
            ->assertJson([
                'article' => [
                    'slug' => 'new-title',
                    'title' => 'new title',
                    'description' => 'new description',
                    'body' => 'new body with random text',
                ]
            ]);
    }

    /** @test */
    public function it_returns_appropriate_field_validation_errors_when_updating_the_article_with_invalid_inputs()
    {
        $article = $this->loggedInUser->articles()->save(factory(\App\Article::class)->make());

        $data = [
            'article' => [
                'title' => '',
                'description' => '',
                'body' => null,
            ]
        ];

        $response = $this->putJson("/api/articles/{$article->slug}", $data, $this->headers);

        $response->assertStatus(422)
            ->assertJson([
                'errors' => [
                    'title' => ['must be a string.'],
                    'description' => ['must be a string.'],
                    'body' => ['must be a string.'],
                ]
            ]);
    }

    /** @test */
    public function it_returns_an_unauthorized_error_when_trying_to_update_article_without_logging_in()
    {
        $article = $this->loggedInUser->articles()->save(factory(\App\Article::class)->make());

        $data = [
            'article' => [
                'title' => 'new title',
                'description' => 'new description',
                'body' => 'new body with random text',
            ]
        ];

        $response = $this->putJson("/api/articles/{$article->slug}", $data);

        $response->assertStatus(401);
    }

    /** @test */
    public function it_returns_a_forbidden_error_when_trying_to_update_articles_by_others()
    {
        $article = $this->user->articles()->save(factory(\App\Article::class)->make());

        $data = [
            'article' => [
                'title' => 'new title',
                'description' => 'new description',
                'body' => 'new body with random text',
            ]
        ];

        $response = $this->putJson("/api/articles/{$article->slug}", $data, $this->headers);

        $response->assertStatus(403);
    }
}
