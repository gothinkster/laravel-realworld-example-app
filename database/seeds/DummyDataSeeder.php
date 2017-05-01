<?php

use Illuminate\Database\Seeder;

class DummyDataSeeder extends Seeder
{
    /**
     * Total number of users.
     *
     * @var int
     */
    protected $totalUsers = 25;

    /**
     * Total number of tags.
     *
     * @var int
     */
    protected $totalTags = 10;

    /**
     * Percentage of users with articles.
     *
     * @var float Value should be between 0 - 1.0
     */
    protected $userWithArticleRatio = 0.8;

    /**
     * Maximum articles that can be created by a user.
     *
     * @var int
     */
    protected $maxArticlesByUser = 15;

    /**
     * Maximum tags that can be attached to an article.
     *
     * @var int
     */
    protected $maxArticleTags = 3;

    /**
     * Maximum number of comments that can be added to an article.
     *
     * @var int
     */
    protected $maxCommentsInArticle = 10;

    /**
     * Percentage of users with favorites.
     *
     * @var float Value should be between 0 - 1.0
     */
    protected $usersWithFavoritesRatio = 0.75;

    /**
     * Percentage of users with following.
     *
     * @var float Value should be between 0 - 1.0
     */
    protected $usersWithFollowingRatio = 0.75;

    /**
     * Populate the database with dummy data for testing.
     * Complete dummy data generation including relationships.
     * Set the property values as required before running database seeder.
     *
     * @param \Faker\Generator $faker
     */
    public function run(\Faker\Generator $faker)
    {
        $users = factory(\App\User::class)->times($this->totalUsers)->create();

        $tags = factory(\App\Tag::class)->times($this->totalTags)->create();

        $users->random((int) $this->totalUsers * $this->userWithArticleRatio)
            ->each(function ($user) use ($faker, $tags) {
                $user->articles()
                    ->saveMany(
                        factory(\App\Article::class)
                        ->times($faker->numberBetween(1, $this->maxArticlesByUser))
                        ->make()
                    )
                    ->each(function ($article) use ($faker, $tags) {
                        $article->tags()->attach(
                            $tags->random($faker->numberBetween(1, min($this->maxArticleTags, $this->totalTags)))
                        );
                    })
                    ->each(function ($article) use ($faker) {
                        $article->comments()
                            ->saveMany(
                                factory(\App\Comment::class)
                                ->times($faker->numberBetween(1, $this->maxCommentsInArticle))
                                ->make()
                            );
                    });
            });

        $articles = \App\Article::all();

        $users->random((int) $users->count() * $this->usersWithFavoritesRatio)
            ->each(function ($user) use($faker, $articles) {
                $articles->random($faker->numberBetween(1, (int) $articles->count() * 0.5))
                    ->each(function ($article) use ($user) {
                        $user->favorite($article);
                    });
            });

        $users->random((int) $users->count() * $this->usersWithFollowingRatio)
            ->each(function ($user) use($faker, $users) {
                $users->except($user->id)
                    ->random($faker->numberBetween(1, (int) ($users->count() - 1) * 0.2))
                    ->each(function ($userToFollow) use ($user) {
                        $user->follow($userToFollow);
                    });
            });
    }
}
