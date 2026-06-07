<?php

namespace Database\Factories\Content;

use App\Models\Content\Post;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Content\Post>
 */
class PostFactory extends Factory
{
    protected $model = Post::class;

    public function definition(): array
    {
        return [
            'title' => fake()->sentence(),
            'body' => fake()->paragraph(),
            'status' => 'open',
            'category_id' => \App\Models\Content\Category::factory(),
            'user_id' => \App\Models\Auth\User::factory(),
        ];
    }
}
