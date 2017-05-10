<?php

namespace Tests\Feature\Api;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class ProfileTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function it_returns_a_valid_profile()
    {
        $response = $this->getJson("/api/profiles/{$this->user->username}");

        $response->assertStatus(200)
            ->assertJson([
                'profile' => [
                    'username' => $this->user->username,
                    'bio' => $this->user->bio,
                    'image' => $this->user->image,
                    'following' => false,
                ]
            ]);
    }

    /** @test */
    public function it_returns_a_not_found_error_on_invalid_profile()
    {
        $response = $this->getJson('/api/profiles/somerandomusername');

        $response->assertStatus(404);
    }

    /** @test */
    public function it_returns_the_profile_following_property_accordingly_when_followed_and_unfollowed()
    {
        $response = $this->postJson("/api/profiles/{$this->user->username}/follow", [], $this->headers);

        $response->assertStatus(200)
            ->assertJson([
                'profile' => [
                    'username' => $this->user->username,
                    'bio' => $this->user->bio,
                    'image' => $this->user->image,
                    'following' => true,
                ]
            ]);

        $this->assertTrue($this->loggedInUser->isFollowing($this->user), 'Failed to follow user');

        $response = $this->deleteJson("/api/profiles/{$this->user->username}/follow", [], $this->headers);

        $response->assertStatus(200)
            ->assertJson([
                'profile' => [
                    'username' => $this->user->username,
                    'bio' => $this->user->bio,
                    'image' => $this->user->image,
                    'following' => false,
                ]
            ]);

        $this->assertFalse($this->loggedInUser->isFollowing($this->user), 'Failed to unfollow user');
    }

    /** @test */
    public function it_returns_a_not_found_error_when_trying_to_follow_and_unfollow_an_invalid_user()
    {
        $response = $this->postJson("/api/profiles/somerandomusername/follow", [], $this->headers);

        $response->assertStatus(404);

        $response = $this->deleteJson("/api/profiles/somerandomusername/follow", [], $this->headers);

        $response->assertStatus(404);
    }

    /** @test */
    public function it_returns_an_unauthorized_error_when_trying_to_follow_or_unfollow_without_logging_in()
    {
        $response = $this->postJson("/api/profiles/{$this->user->username}/follow");

        $response->assertStatus(401);

        $response = $this->deleteJson("/api/profiles/{$this->user->username}/follow");

        $response->assertStatus(401);
    }
}
