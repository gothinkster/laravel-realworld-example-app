<?php

namespace Tests\Feature\Api;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class ArticleCreateTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function it_returns_the_article_on_successfully_creating_a_new_article()
    {
        $data = [
            'article' => [
                'title' => 'test title',
                'description' => 'test description',
                'body' => 'test body with random text',
            ]
        ];

        $response = $this->postJson('/api/articles', $data, $this->headers);

        $response->assertStatus(200)
            ->assertJson([
                'article' => [
                    'slug' => 'test-title',
                    'title' => 'test title',
                    'description' => 'test description',
                    'body' => 'test body with random text',
                    'tagList' => [],
                    'favorited' => false,
                    'favoritesCount' => 0,
                    'author' => [
                        'username' => $this->loggedInUser->username,
                        'bio' => $this->loggedInUser->bio,
                        'image' => $this->loggedInUser->image,
                        'following' => false,
                    ]
                ]
            ]);

        $data['article']['tagList'] = ['test', 'coding'];

        $response = $this->postJson('/api/articles', $data, $this->headers);

        $response->assertStatus(200)
            ->assertJson([
                'article' => [
                    'slug' => 'test-title-1',
                    'title' => 'test title',
                    'tagList' => ['test', 'coding'],
                    'author' => [
                        'username' => $this->loggedInUser->username,
                    ]
                ]
            ]);
    }

    /** @test */
    public function it_returns_appropriate_field_validation_errors_when_creating_a_new_article_with_invalid_inputs()
    {
        $data = [
            'article' => [
                'title' => '',
                'description' => '',
            ]
        ];

        $response = $this->postJson('/api/articles', $data, $this->headers);

        $response->assertStatus(422)
            ->assertJson([
                'errors' => [
                    'title' => ['field is required.'],
                    'description' => ['field is required.'],
                    'body' => ['field is required.'],
                ]
            ]);

        $data['article']['tagList'] = 'invalid tag';

        $response = $this->postJson('/api/articles', $data, $this->headers);

        $response->assertStatus(422)
            ->assertJson([
                'errors' => [
                    'tagList' => ['list must be an array.'],
                ]
            ]);
    }

    /** @test */
    public function it_returns_an_unauthorized_error_when_trying_to_add_article_without_logging_in()
    {
        $response = $this->postJson('/api/articles', []);

        $response->assertStatus(401);
    }
}
