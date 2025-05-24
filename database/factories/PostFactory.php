<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Post>
 */
class PostFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(),
            'content' => fake()->paragraph(3),
            'image_url' => fake()->imageUrl(640, 480, 'cars', true),
            'scheduled_time' => now()->addDays(rand(1, 5)),
            'status' => fake()->randomElement(['draft', 'scheduled', 'published']),
            'user_id' => User::factory(),
        ];
    }
}
