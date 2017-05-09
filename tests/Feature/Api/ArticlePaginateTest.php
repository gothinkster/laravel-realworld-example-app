<?php

namespace Tests\Feature\Api;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class ArticlePaginateTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function it_returns_the_correct_articles_with_limit_and_offset()
    {
        $this->user->articles()->saveMany(factory(\App\Article::class)->times(25)->make());

        $response = $this->getJson('/api/articles');

        $response->assertStatus(200)
            ->assertJson([
                'articlesCount' => 25
            ]);

        $this->assertCount(20, $response->json()['articles'], 'Expected articles to set default limit to 20');

        $this->assertEquals(
            $this->user->articles()->latest()->take(20)->pluck('slug')->toArray(),
            array_column($response->json()['articles'], 'slug'),
            'Expected latest 20 articles by default'
        );

        $response = $this->getJson('/api/articles?limit=10&offset=5');

        $response->assertStatus(200)
            ->assertJson([
                'articlesCount' => 25
            ]);

        $this->assertCount(10, $response->json()['articles'], 'Expected article limit of 10 when set');

        $this->assertEquals(
            $this->user->articles()->latest()->skip(5)->take(10)->pluck('slug')->toArray(),
            array_column($response->json()['articles'], 'slug'),
            'Expected latest 10 articles with 5 offset'
        );
    }
}
