<?php

namespace Tests\Feature\Api;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class TagTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function it_returns_an_array_of_tags()
    {
        $tags = factory(\App\Tag::class)->times(5)->create();

        $response = $this->getJson('/api/tags');

        $response->assertStatus(200)
            ->assertJson([
                'tags' => $tags->pluck('name')->toArray()
            ]);
    }

    /** @test */
    public function it_returns_an_empty_array_of_tags_when_there_are_none_in_database()
    {
        $response = $this->getJson('/api/tags');

        $response->assertStatus(200)
            ->assertJson([
                'tags' => []
            ]);

        $this->assertEmpty($response->json()['tags'], 'Expected empty tags array');
    }
}
