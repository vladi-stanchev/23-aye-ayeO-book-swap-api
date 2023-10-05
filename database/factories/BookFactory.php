<?php

namespace Database\Factories;

use App\Models\Genre;
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
            'blurb' => $this->faker->text(500),
            'image' => $this->faker->imageUrl(),
            'claimed_by_name' => $this->faker->firstName(),
            'claimed_by_email' => $this->faker->email(),
            'page_count' => $this->faker->randomNumber(3, true),
            'year' => $this->faker->year(),
            'genre_id' => Genre::factory()
        ];
    }
}
