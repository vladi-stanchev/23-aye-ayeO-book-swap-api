<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Review;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class ReviewTest extends TestCase
{
    use DatabaseMigrations;

    // 201
    public function test_review_add_success(): void
    {
        $book = Book::factory()->create();

        $response = $this->postJson('/api/reviews', [
            'name' => 'Josh',
            'rating' => 5,
            'review' => "Very wonderful amazing!",
            'book_id' => $book->id
        ]);
        $response->assertStatus(201);
        $response->assertJson(function (AssertableJson $json) {
            $json->has('message')
                ->where(
                    'message',
                    "Review created"
                );
        });
    }

    // 201
    public function test_review_add_db_success()
    {
        $book = Book::factory()->create();

        $response = $this->postJson('/api/reviews', [
            'name' => 'Josh',
            'rating' => 0,
            'review' => 'Nohnohnohh',
            'book_id' => $book->id
        ]);

        $this->assertDatabaseHas('reviews', [
            'name' => 'Josh',
            'rating' => 0,
            'review' => 'Nohnohnohh',
            'book_id' => $book->id
        ]);
    }

    // 422
    public function test_review_add_no_data()
    {
        $response = $this->postJson('/api/reviews', []);
        $response->assertStatus(422)
            ->assertInvalid(['name', 'rating', 'review', 'book_id']);
    }

    // 422
    public function test_review_add_invalid_data()
    {
        $response = $this->postJson('/api/reviews', [
            'name' => 9,
            'rating' => 10,
            'review' => 'No',
            'book_id' => 'Not known'
        ]);
        $response->assertStatus(422)
            ->assertInvalid(['name', 'rating', 'review', 'book_id']);
    }
}
