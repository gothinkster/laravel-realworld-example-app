<?php

namespace Tests\Feature\Api;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class ArticleFilterTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function it_returns_an_empty_array_of_articles_when_no_articles_exist_with_the_tag_or_an_invalid_tag()
    {
        $response = $this->getJson('/api/articles?tag=test');

        $response->assertStatus(200)
            ->assertJson([
                'articles' => [],
                'articlesCount' => 0
            ]);

        $response = $this->getJson('/api/articles?tag=somerandomtag');

        $response->assertStatus(200)
            ->assertJson([
                'articles' => [],
                'articlesCount' => 0
            ]);
    }

    /** @test */
    public function it_returns_the_articles_with_the_tag_along_with_correct_total_article_count()
    {
        $tags = factory(\App\Tag::class)->times(2)->create();

        $articles = $this->user->articles()
            ->saveMany(factory(\App\Article::class)->times(3)->make())
            ->each(function ($article) use ($tags) {
                $article->tags()->attach($tags);
            });

        $this->user->articles()->saveMany(factory(\App\Article::class)->times(5)->make());

        $response = $this->getJson("/api/articles?tag={$tags[0]->name}");

        $response->assertStatus(200)
            ->assertJson([
                'articles' => [
                    [
                        'slug' => $articles[2]->slug,
                        'title' => $articles[2]->title,
                        'tagList' => $articles[2]->tagList,
                    ],
                    [
                        'slug' => $articles[1]->slug,
                        'title' => $articles[1]->title,
                        'tagList' => $articles[1]->tagList,
                    ],
                    [
                        'slug' => $articles[0]->slug,
                        'title' => $articles[0]->title,
                        'tagList' => $articles[0]->tagList,
                    ],
                ],
                'articlesCount' => 3
            ]);
    }

    /** @test */
    public function it_returns_an_empty_array_of_articles_when_no_articles_exist_by_the_author_or_invalid_author()
    {
        $response = $this->getJson('/api/articles?author=test');

        $response->assertStatus(200)
            ->assertJson([
                'articles' => [],
                'articlesCount' => 0
            ]);

        $response = $this->getJson('/api/articles?author=somerandomtag');

        $response->assertStatus(200)
            ->assertJson([
                'articles' => [],
                'articlesCount' => 0
            ]);
    }

    /** @test */
    public function it_returns_the_articles_by_the_author_along_with_correct_total_article_count()
    {
        $articles = $this->user->articles()->saveMany(factory(\App\Article::class)->times(3)->make());
        $this->loggedInUser->articles()->saveMany(factory(\App\Article::class)->times(5)->make());

        $response = $this->getJson("/api/articles?author={$this->user->username}");

        $response->assertStatus(200)
            ->assertJson([
                'articles' => [
                    [
                        'slug' => $articles[2]->slug,
                        'title' => $articles[2]->title,
                        'author' => [
                            'username' => $this->user->username
                        ]
                    ],
                    [
                        'slug' => $articles[1]->slug,
                        'title' => $articles[1]->title,
                        'author' => [
                            'username' => $this->user->username
                        ]
                    ],
                    [
                        'slug' => $articles[0]->slug,
                        'title' => $articles[0]->title,
                        'author' => [
                            'username' => $this->user->username
                        ]
                    ],
                ],
                'articlesCount' => 3
            ]);
    }

    /** @test */
    public function it_returns_an_empty_array_of_articles_when_no_favorited_articles_exist_for_a_user_or_invalid_user()
    {
        $response = $this->getJson("/api/articles?favorited={$this->user->username}");

        $response->assertStatus(200)
            ->assertJson([
                'articles' => [],
                'articlesCount' => 0
            ]);

        $response = $this->getJson('/api/articles?favorited=somerandomuser');

        $response->assertStatus(200)
            ->assertJson([
                'articles' => [],
                'articlesCount' => 0
            ]);
    }

    /** @test */
    public function it_returns_the_articles_favorited_by_the_user_along_with_correct_total_article_count()
    {
        $articles = $this->loggedInUser->articles()->saveMany(factory(\App\Article::class)->times(5)->make());
        $this->user->favorite($articles[0]);
        $this->user->favorite($articles[2]);
        $this->user->favorite($articles[4]);

        $response = $this->getJson("/api/articles?favorited={$this->user->username}");

        $response->assertStatus(200)
            ->assertJson([
                'articles' => [
                    [
                        'slug' => $articles[4]->slug,
                        'title' => $articles[4]->title,
                        'author' => [
                            'username' => $this->loggedInUser->username
                        ]
                    ],
                    [
                        'slug' => $articles[2]->slug,
                        'title' => $articles[2]->title,
                        'author' => [
                            'username' => $this->loggedInUser->username
                        ]
                    ],
                    [
                        'slug' => $articles[0]->slug,
                        'title' => $articles[0]->title,
                        'author' => [
                            'username' => $this->loggedInUser->username
                        ]
                    ],
                ],
                'articlesCount' => 3
            ]);
    }
}
