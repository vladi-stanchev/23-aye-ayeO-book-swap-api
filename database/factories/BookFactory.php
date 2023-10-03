<?php

namespace Database\Factories;

use App\Models\Genre;
use App\Models\Review;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Book>
 */
class BookFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => $this->faker->text(200),
            'author' => $this->faker->name(),
            'image' => $this->faker->imageUrl(),
            'genre_id' => Genre::factory(),
            'blurb' => $this->faker->text(500),
            'claimed_by_name' => $this->faker->name(),
            'page_count' => $this->faker->randomDigit(),
            'year' => $this->faker->randomDigit(),
            'review_id' => Review::factory()
        ];
    }
}
