<?php

namespace Tests\Feature\Api;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class ArticleTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function it_returns_an_empty_array_of_articles_when_no_articles_exist()
    {
        $response = $this->getJson('/api/articles');

        $response->assertStatus(200)
            ->assertJson([
                'articles' => [],
                'articlesCount' => 0
            ]);
    }
    
    /** @test */
    public function it_returns_the_articles_and_correct_total_article_count()
    {
        $articles = $this->user->articles()->saveMany(factory(\App\Article::class)->times(2)->make());

        $response = $this->getJson('/api/articles');

        $response->assertStatus(200)
            ->assertJson([
                'articles' => [
                    [
                        'slug' => $articles[0]->slug,
                        'title' => $articles[0]->title,
                        'description' => $articles[0]->description,
                        'body' => $articles[0]->body,
                        'tagList' => $articles[0]->tagList,
                        'createdAt' => $articles[0]->created_at->toAtomString(),
                        'updatedAt' => $articles[0]->updated_at->toAtomString(),
                        'favorited' => false,
                        'favoritesCount' => 0,
                        'author' => [
                            'username' => $this->user->username,
                            'bio' => $this->user->bio,
                            'image' => $this->user->image,
                            'following' => false,
                        ]
                    ],
                    [
                        'slug' => $articles[1]->slug,
                        'title' => $articles[1]->title,
                    ]
                ],
                'articlesCount' => 2
            ]);
    }

    /** @test */
    public function it_returns_the_correct_following_and_favorited_fields_when_logged_in()
    {
        $article = $this->user->articles()->save(factory(\App\Article::class)->make());

        $this->loggedInUser->follow($this->user);

        $this->loggedInUser->favorite($article);

        $response = $this->getJson('/api/articles');

        $response->assertStatus(200)
            ->assertJson([
                'articles' => [
                    [
                        'slug' => $article->slug,
                        'title' => $article->title,
                        'favorited' => false,
                        'favoritesCount' => 1,
                        'author' => [
                            'username' => $this->user->username,
                            'following' => false,
                        ]
                    ]
                ],
                'articlesCount' => 1
            ]);

        $response = $this->getJson('/api/articles', $this->headers);

        $response->assertStatus(200)
            ->assertJson([
                'articles' => [
                    [
                        'slug' => $article->slug,
                        'title' => $article->title,
                        'favorited' => true,
                        'favoritesCount' => 1,
                        'author' => [
                            'username' => $this->user->username,
                            'following' => true,
                        ]
                    ]
                ],
                'articlesCount' => 1
            ]);
    }

    // Add article returns valid article
    // Unique slug generation.
    // Correct validation errors on adding article
    // Get article by slug
    // Not found error on invalid slug
    // Correct limit and offset
    // Unauthorized error on adding article with no auth
    // Unauthorized error on updating article with no auth
    // Unauthorized error on removing article with no auth
    // Forbidden error on removing article by another user
}
