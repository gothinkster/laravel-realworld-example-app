<?php

namespace Tests\Feature\Api;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class UserTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function it_returns_the_current_user_when_logged_in()
    {
        $response = $this->getJson('/api/user', $this->headers);

        $response->assertStatus(200)
            ->assertJson([
                'user' => [
                    'email' => $this->loggedInUser->email,
                    'username' => $this->loggedInUser->username,
                    'bio' => $this->loggedInUser->bio,
                    'image' => $this->loggedInUser->image,
                ]
            ]);
    }

    /** @test */
    public function it_returns_invalid_token_error_when_using_a_wrong_token()
    {
        $response = $this->getJson('/api/user', [
            'Authorization' => 'Bearer InsertWrongTokenHereToTestPleaseSendHelp'
        ]);

        $response->assertStatus(401)
            ->assertJson([
                'errors' => [
                    'message' => 'JWT error: Token is invalid',
                ]
            ]);
    }

    /** @test */
    public function it_returns_an_unauthorized_error_when_not_logged_in()
    {
        $response = $this->getJson('/api/user');

        $response->assertStatus(401);
    }

    /** @test */
    public function it_returns_the_updated_user_on_updating()
    {
        $data = [
            'user' => [
                'username' => 'test12345',
                'email' => 'test12345@test.com',
                'password' => 'test12345',
                'bio' => 'hello',
                'image' => 'http://test.com/test.jpg',
            ]
        ];

        $response = $this->putJson('/api/user', $data, $this->headers);

        $response->assertStatus(200)
            ->assertJson([
                'user' => [
                    'username' => 'test12345',
                    'email' => 'test12345@test.com',
                    'bio' => 'hello',
                    'image' => 'http://test.com/test.jpg',
                ]
            ]);

        $this->assertTrue(auth()->once($data['user']), 'Password update failed');
    }

    /** @test */
    public function it_returns_appropriate_field_validation_errors_on_updating()
    {
        $data = [
            'user' => [
                'username' => 'test test',
                'email' => 'invalid email passing by',
                'password' => '',
                'bio' => 'nothing wrong with this one',
                'image' => 'this is an invalid url',
            ]
        ];

        $response = $this->putJson('/api/user', $data, $this->headers);

        $response->assertStatus(422)
            ->assertJson([
                'errors' => [
                    'username' => ['may only contain letters and numbers.'],
                    'email' => ['must be a valid email address.'],
                    'password' => ['must be at least 6 characters.'],
                    'image' => ['format is invalid.'],
                ]
            ]);
    }

    /** @test */
    public function it_returns_username_and_email_taken_validation_errors_when_using_duplicate_values_on_updating()
    {
        $data = [
            'user' => [
                'username' => $this->user->username,
                'email' => $this->user->email,
                'password' => 'secret',
            ]
        ];

        $response = $this->putJson('/api/user', $data, $this->headers);

        $response->assertStatus(422)
            ->assertJson([
                'errors' => [
                    'username' => ['has already been taken.'],
                    'email' => ['has already been taken.'],
                ]
            ]);
    }
}
