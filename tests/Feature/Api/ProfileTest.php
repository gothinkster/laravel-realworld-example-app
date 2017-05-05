<?php

namespace Tests\Feature\Api;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class ProfileTest extends TestCase
{
    use DatabaseMigrations;

    protected $users;

    public function setUp()
    {
        parent::setUp();

        $this->users = factory(\App\User::class)->times(2)->create();
    }

    /** @test  */
    public function it_returns_a_valid_profile()
    {
        $user = $this->users[0];

        $response = $this->getJson('/api/profiles/' . $user->username);

        $response->assertStatus(200)
            ->assertJson([
                'profile' => [
                    'username' => $user->username,
                    'bio' => $user->bio,
                    'image' => $user->image,
                    'following' => false,
                ]
            ]);
    }
}
