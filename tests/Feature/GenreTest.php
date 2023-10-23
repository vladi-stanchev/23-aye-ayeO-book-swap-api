<?php

namespace Tests\Feature;

use App\Models\Genre;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class GenreTest extends TestCase
{

    use DatabaseMigrations;

    public function test_get_all_genres_success(): void
    {
        Genre::factory()->count(20)->create();

        $response = $this->getJson('/api/genres');

        $response->assertStatus(200)

            ->assertJson(function (AssertableJson $json) {

                $json->hasAll(['message', 'data'])
                    ->whereAllType([
                        'message' => 'string'
                    ])

                    ->has('data', 20, function (AssertableJson $json) {
                        $json->hasAll(['id', 'name'])
                            ->whereAllType([
                                'id' => 'integer',
                                'name' => 'string'
                            ]);
                    });
            });
    }
}
