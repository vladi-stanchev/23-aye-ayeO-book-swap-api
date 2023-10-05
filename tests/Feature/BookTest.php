<?php

namespace Tests\Feature;

use App\Models\Book;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

use function PHPUnit\Framework\assertJson;

class BookTest extends TestCase
{
    use DatabaseMigrations;

    public function test_get_all_books_success(): void
    {
        // Book::factory()->count(2)->create();
        Book::factory(['claimed' => 0])->count(2)->create();
        Book::factory(['claimed' => 1])->count(2)->create();

        $response = $this->getJson('/api/books');

        $response->assertStatus(200)

            ->assertJson(function (AssertableJson $json) {

                $json->hasAll(['message', 'data'])
                    ->whereAllType([
                        'message' => 'string'
                    ])
                    ->has('data', 2, function (AssertableJson $json) {
                        $json->hasAll(['id', 'title', 'author', 'image', 'genre'])
                            ->whereAllType([
                                'id' => 'integer',
                                'title' => 'string',
                                'author' => 'string',
                                'image' => 'string'
                            ])
                            ->has('genre', function (AssertableJson $json) {
                                $json->hasAll(['id', 'name'])
                                    ->whereAllType([
                                        'id' => 'integer',
                                        'name' => 'string'
                                    ]);
                            });
                    });
            });
    }

    public function test_no_books_found(): void
    {
        $response = $this->getJson('api/books/');
        $response->assertStatus(404);
    }

    public function test_get_book_by_id(): void
    {
        $book = Book::factory()->create();

        $response = $this->getJson('api/books/' . $book->id);

        $response->assertStatus(200)

            ->assertJson(function (AssertableJson $json) {
                $json->hasAll(['message', 'data'])
                    ->whereAllType([
                        'message' => 'string'
                    ])

                    ->has('data', function (AssertableJson $json) {
                        $json->hasAll(['id', 'title', 'author', 'blurb', 'image', 'claimed_by_name', 'page_count', 'year', 'genre'])
                            ->whereAllType([
                                'id' => 'integer',
                                'title' => 'string',
                                'author' => 'string',
                                'blurb' => 'string',
                                'image' => 'string',
                                'claimed_by_name' => 'string',
                                'page_count' => 'integer',
                                'year' => 'integer'
                            ])
                            ->has('genre', function (AssertableJson $json) {
                                $json->hasAll(['id', 'name'])
                                    ->whereAllType([
                                        'id' => 'integer',
                                        'name' => 'string'
                                    ]);
                            });
                    });
            });
    }

    public function test_book_not_found()
    {
        $response = $this->getJson('/api/books/564756445323254');
        $response->assertStatus(404);
        $response->assertJson(function (AssertableJson $json) {
            $json->has('message')
                ->where(
                    'message',
                    'Book with id 564756445323254 not found'
                );
        });
    }

    public function test_book_not_found_claim()
    {
        $response = $this->putJson('/api/books/claim/564756445323254', [
            'name' => 'test',
            'email' => 'test@test.com'
        ]);
        $response->assertStatus(404);
        $response->assertJson(function (AssertableJson $json) {
            $json->has('message')
                ->where(
                    'message',
                    'Book 564756445323254 was not found'
                );
        });
    }

    public function test_book_already_claimed()
    {
        $book = Book::factory()->create();

        $response = $this->putJson("api/books/claim/$book->id", [
            'name' => 'test',
            'email' => 'test@test.com'
        ]);
        $response->assertStatus(400);
        $response->assertJson(function (AssertableJson $json) use ($book) {
            $json->has('message')
                ->where(
                    'message',
                    "Book $book->id is already claimed"
                );
        });
    }

    public function test_book_claim_success()
    {
        $book = Book::factory(['claimed_by_name' => null])->create();

        $response = $this->putJson("api/books/claim/$book->id", [
            'name' => 'test',
            'email' => 'test@test.com'
        ]);
        $response->assertOk();
        $response->assertJson(function (AssertableJson $json) use ($book) {
            $json->has('message')
                ->where(
                    'message',
                    "Book $book->id was claimed"
                );
        });
    }

    public function test_book_claim_no_name_no_email()
    {
        $response = $this->putJson('api/books/claim/1', [
            'name' => '',
            'email' => ''
        ]);
        $response->assertStatus(422);
        $response->assertInvalid(['name', 'email']);
    }

    public function test_book_claim_invalid_email()
    {
        $response = $this->putJson('api/books/claim/1', [
            'name' => 'test',
            'email' => 'test'
        ]);
        $response->assertStatus(422);
        $response->assertInvalid('email');
    }

    public function test_book_claim_db_success()
    {
        $book = Book::factory(['claimed_by_name' => null])->create();

        $this->putJson("api/books/claim/$book->id", [
            'name' => 'test',
            'email' => 'test@test.com'
        ]);

        $this->assertDatabaseHas('books', [
            'claimed_by_name' => 'test',
            'claimed_by_email' => 'test@test.com',
        ]);
    }
}
