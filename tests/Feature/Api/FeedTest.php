<?php

namespace Tests\Feature\Api;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class FeedTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function it_returns_an_empty_array_of_articles_when_user_does_not_follow_anyone()
    {
        $response = $this->getJson('/api/articles/feed', $this->headers);

        $response->assertStatus(200)
            ->assertJson([
                'articles' => [],
                'articlesCount' => 0
            ]);
    }

    /** @test */
    public function it_returns_articles_of_users_followed_by_the_logged_in_user()
    {
        $articles = $this->user->articles()->saveMany(factory(\App\Article::class)->times(2)->make());

        $this->loggedInUser->follow($this->user);

        $response = $this->getJson('/api/articles/feed', $this->headers);

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
                            'following' => true,
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
    public function it_returns_the_correct_feed_articles_with_limit_and_offset()
    {
        $this->user->articles()->saveMany(factory(\App\Article::class)->times(25)->make());

        $this->loggedInUser->follow($this->user);

        $response = $this->getJson('/api/articles/feed', $this->headers);

        $response->assertStatus(200)
            ->assertJson([
                'articlesCount' => 25
            ]);

        $this->assertCount(20, $response->json()['articles'], 'Expected feed to set default limit to 20');

        $this->assertEquals(
            $this->user->articles()->latest()->take(20)->pluck('slug')->toArray(),
            array_column($response->json()['articles'], 'slug'),
            'Expected latest 20 feed articles by default'
        );

        $response = $this->getJson('/api/articles/feed?limit=10&offset=5', $this->headers);

        $response->assertStatus(200)
            ->assertJson([
                'articlesCount' => 25
            ]);

        $this->assertCount(10, $response->json()['articles'], 'Expected feed limit of 10 when set');

        $this->assertEquals(
            $this->user->articles()->latest()->skip(5)->take(10)->pluck('slug')->toArray(),
            array_column($response->json()['articles'], 'slug'),
            'Expected latest 10 feed articles with 5 offset'
        );
    }

    /** @test */
    public function it_returns_the_feed_articles_with_appropriate_favorite_and_following_fields()
    {
        $article = $this->user->articles()->save(factory(\App\Article::class)->make());

        $this->loggedInUser->follow($this->user);

        $response = $this->getJson('/api/articles/feed', $this->headers);

        $response->assertStatus(200)
            ->assertJson([
                'articles' => [
                    [
                        'slug' => $article->slug,
                        'favorited' => false,
                        'favoritesCount' => 0,
                        'author' => [
                            'username' => $this->user->username,
                            'following' => true,
                        ]
                    ],
                ]
            ]);

        $this->loggedInUser->favorite($article);

        $response = $this->getJson('/api/articles/feed', $this->headers);

        $response->assertStatus(200)
            ->assertJson([
                'articles' => [
                    [
                        'slug' => $article->slug,
                        'favorited' => true,
                        'favoritesCount' => 1,
                        'author' => [
                            'username' => $this->user->username,
                            'following' => true,
                        ]
                    ],
                ]
            ]);
    }

    /** @test */
    public function it_returns_an_unauthorized_error_when_trying_to_get_feed_without_logging_in()
    {
        $response = $this->getJson('/api/articles/feed');

        $response->assertStatus(401);
    }
}
