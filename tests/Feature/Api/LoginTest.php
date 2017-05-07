<?php

namespace Tests\Feature\Api;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class LoginTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function it_returns_a_user_with_valid_token_on_valid_login()
    {
        $data = [
            'user' => [
                'email' => $this->user->email,
                'password' => 'secret',
            ]
        ];

        $response = $this->postJson('/api/users/login', $data);

        $response->assertStatus(200)
            ->assertJson([
            'user' => [
                'email' => $this->user->email,
                'username' => $this->user->username,
                'bio' => $this->user->bio,
                'image' => $this->user->image,
            ]
        ]);

        $this->assertArrayHasKey('token', $response->json()['user'], 'Token not found');

        $this->assertTrue(
            (count(explode('.', $response->json()['user']['token'])) === 3),
             'Failed to validate token'
        );
    }

    /** @test */
    public function it_returns_field_required_validation_errors_on_invalid_login()
    {
        $data = [];

        $response = $this->postJson('/api/users/login', $data);

        $response->assertStatus(422)
            ->assertJson([
                'errors' => [
                    'email' => ['field is required.'],
                    'password' => ['field is required.'],
                ]
            ]);
    }

    /** @test */
    public function it_returns_appropriate_field_validation_errors_on_invalid_login()
    {
        $data = [
            'user' => [
                'email' => 'invalid email',
                'password' => 'secret',
            ]
        ];

        $response = $this->postJson('/api/users', $data);

        $response->assertStatus(422)
            ->assertJson([
                'errors' => [
                    'email' => ['must be a valid email address.'],
                ]
            ]);
    }
}
