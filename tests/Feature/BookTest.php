<?php

namespace Tests\Feature;

use App\Models\Book;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;


class BookTest extends TestCase
{
    use DatabaseMigrations;

    public function test_get_all_books_success(): void
    {
        Book::factory()->count(2)->create();

        $response = $this->getJson('/api/books');

        $response->assertStatus(200)

            ->assertJson(function (AssertableJson $json) {

                $json->hasAll(['message', 'data'])
                    ->whereAllType([
                        'message' => 'string'
                    ])
                    ->has('data', 2, function (AssertableJson $json) {
                        $json->hasAll(['id', 'title', 'author', 'image', 'genre'])
                            ->whereAllType(
                                [
                                    'id' => 'integer',
                                    'title' => 'string',
                                    'author' => 'string',
                                    'image' => 'string',
                                ]
                            )
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

    public function test_get_single_books_success(): void
    {
        Book::factory()->create();

        $response = $this->getJson('/api/books/{id}');

        $response->assertStatus(200)
            ->assertJson(function (AssertableJson $json) {

                $json->hasAll(['message', 'data'])
                    ->whereAllType([
                        'message' => 'string'
                    ])
                    ->has('data', function (AssertableJson $json) {
                        $json->hasAll(
                            [
                                'id',
                                'title',
                                'author',
                                'blurb',
                                'claimed_by_name',
                                'image',
                                'page_count',
                                'year',
                                'genre',
                                'reviews'
                            ]
                        )
                            ->whereAllType(
                                [
                                    'id' => 'integer',
                                    'title' => 'string',
                                    'author' => 'string',
                                    'blurb' => 'string',
                                    'claimed_by_name' => 'string',
                                    'image' => 'string',
                                    'page_count' => 'integer',
                                    'year' => 'integer'

                                ]
                            )
                            ->has('genre', function (AssertableJson $json) {
                                $json->hasAll(['id', 'name'])
                                    ->whereAllType(
                                        [
                                            'id' => 'integer',
                                            'name' => 'string'
                                        ]
                                    );
                            })
                            ->has('reviews', function (AssertableJson $json) {
                                $json->hasAll(['id', 'name', 'rating', 'review'])
                                    ->whereAllType(
                                        [
                                            'id' => 'integer',
                                            'name' => 'string',
                                            'rating' => 'integer',
                                            'review' => 'string'
                                        ]
                                    );
                            });
                    });
            });
    }
}
