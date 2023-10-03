<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Review>
 */
class ReviewFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
                return [
                    'name' => $this->faker->name(),
                    'rating' => $this->faker->numberBetween(1,5),
                    'review' => $this->faker->text(500),
                ];
    }
}
