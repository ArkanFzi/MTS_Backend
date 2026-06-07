<?php

namespace Database\Factories\Gamification;

use App\Models\Gamification\Badge;
use Illuminate\Database\Eloquent\Factories\Factory;

class BadgeFactory extends Factory
{
    protected $model = Badge::class;

    public function definition(): array
    {
        return [
            'name' => fake()->unique()->word(),
            'description' => fake()->sentence(),
            'icon_url' => fake()->imageUrl(),
            'tier' => 'bronze',
            'condition_type' => 'post_count',
            'condition_value' => 5,
        ];
    }
}
