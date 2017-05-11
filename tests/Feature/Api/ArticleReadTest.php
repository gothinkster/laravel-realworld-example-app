<?php

namespace Tests\Feature\Api;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class ArticleReadTest extends TestCase
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
                        'slug' => $articles[1]->slug,
                        'title' => $articles[1]->title,
                        'description' => $articles[1]->description,
                        'body' => $articles[1]->body,
                        'tagList' => $articles[1]->tagList,
                        'createdAt' => $articles[1]->created_at->toAtomString(),
                        'updatedAt' => $articles[1]->updated_at->toAtomString(),
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
                        'slug' => $articles[0]->slug,
                        'title' => $articles[0]->title,
                    ]
                ],
                'articlesCount' => 2
            ]);
    }

    /** @test */
    public function it_returns_the_article_by_slug_if_valid_and_not_found_error_if_invalid()
    {
        $article = $this->user->articles()->save(factory(\App\Article::class)->make());

        $response = $this->getJson("/api/articles/{$article->slug}");

        $response->assertStatus(200)
            ->assertJson([
                'article' => [
                    'slug' => $article->slug,
                    'title' => $article->title,
                    'description' => $article->description,
                    'body' => $article->body,
                    'tagList' => $article->tagList,
                    'createdAt' => $article->created_at->toAtomString(),
                    'updatedAt' => $article->updated_at->toAtomString(),
                    'favorited' => false,
                    'favoritesCount' => 0,
                    'author' => [
                        'username' => $this->user->username,
                        'bio' => $this->user->bio,
                        'image' => $this->user->image,
                        'following' => false,
                    ]
                ]
            ]);

        $response = $this->getJson('/api/articles/randominvalidslug');

        $response->assertStatus(404);
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
}
