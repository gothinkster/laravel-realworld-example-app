<?php

namespace Tests\Feature\Api;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class CommentTest extends TestCase
{
    use DatabaseMigrations;

    protected $article;

    public function setUp()
    {
        parent::setUp();

        $this->article = $this->user->articles()->save(factory(\App\Article::class)->make());
    }

    /** @test */
    public function it_returns_the_comment_on_successfully_adding_a_comment_to_the_article()
    {
        $data = [
            'comment' => [
                'body' => 'This is a comment'
            ]
        ];

        $response = $this->postJson("/api/articles/{$this->article->slug}/comments", $data, $this->headers);

        $response->assertStatus(200)
            ->assertJson([
                'comment' => [
                    'body' => 'This is a comment',
                    'author' => [
                        'username' => $this->loggedInUser->username
                    ],
                ]
            ]);
    }

    /** @test */
    public function it_returns_a_200_success_response_on_successfully_removing_a_comment_from_the_article()
    {
        $comment = $this->article
            ->comments()
            ->save(factory(\App\Comment::class)->make(['user_id' => $this->loggedInUser->id]));

        $response = $this->deleteJson("/api/articles/{$this->article->slug}/comments/{$comment->id}", [], $this->headers);

        $response->assertStatus(200);

        $this->assertEmpty($this->article->comments, 'Failed to delete comment');
    }

    /** @test */
    public function it_returns_all_the_comments_of_the_article()
    {
        $comments = $this->article
            ->comments()
            ->saveMany(factory(\App\Comment::class)->times(2)->make(['user_id' => $this->user->id]));

        $response = $this->getJson("/api/articles/{$this->article->slug}/comments", [], $this->headers);

        $response->assertStatus(200)
            ->assertJson([
                'comments' => [
                    [
                        'body' => $comments[1]['body'],
                        'author' => [
                            'username' => $this->user->username
                        ]
                    ],
                    [
                        'body' => $comments[0]['body'],
                        'author' => [
                            'username' => $this->user->username
                        ]
                    ],
                ]
            ]);
    }

    /** @test */
    public function it_returns_a_forbidden_error_when_trying_to_remove_comments_by_others()
    {
        $comment = $this->article
            ->comments()
            ->save(factory(\App\Comment::class)->make(['user_id' => $this->user->id]));

        $response = $this->deleteJson("/api/articles/{$this->article->slug}/comments/{$comment->id}", [], $this->headers);

        $response->assertStatus(403);

        $this->assertCount(1, $this->article->comments, 'Expected comment to not be deleted by unauthorized user');
    }

    /** @test */
    public function it_returns_an_unauthorized_error_when_trying_to_add_or_remove_comments_without_logging_in()
    {
        $comment = $this->article
            ->comments()
            ->save(factory(\App\Comment::class)->make(['user_id' => $this->loggedInUser->id]));

        $response = $this->postJson("/api/articles/{$this->article->slug}/comments");

        $response->assertStatus(401);

        $response = $this->deleteJson("/api/articles/{$this->article->slug}/comments/{$comment->id}");

        $response->assertStatus(401);
    }

    /** @test */
    public function it_returns_a_not_found_error_when_trying_to_the_get_comments_of_a_non_existing_article()
    {
        $response = $this->getJson("/api/articles/somerandomslug/comments", [], $this->headers);

        $response->assertStatus(404);
    }

    /** @test */
    public function it_returns_a_not_found_error_when_trying_to_remove_a_non_existing_comment()
    {
        $response = $this->deleteJson("/api/articles/{$this->article->slug}/comments/999", [], $this->headers);

        $response->assertStatus(404);

        $response = $this->deleteJson("/api/articles/somerandomslug/comments/999", [], $this->headers);

        $response->assertStatus(404);
    }
}
