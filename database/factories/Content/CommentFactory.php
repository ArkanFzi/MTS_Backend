<?php

namespace Database\Factories\Content;

use App\Models\Content\Comment;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Content\Comment>
 */
class CommentFactory extends Factory
{
    protected $model = Comment::class;

    public function definition(): array
    {
        return [
            'post_id' => \App\Models\Content\Post::factory(),
            'user_id' => \App\Models\Auth\User::factory(),
            'body' => fake()->paragraph(),
            'vote_score' => 0,
        ];
    }
}
