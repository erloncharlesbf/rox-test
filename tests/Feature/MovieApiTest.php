<?php

namespace Tests\Feature;

use App\Models\Movie;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class MovieApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_list_movies(): void
    {
        $user = User::factory()->create();
        Movie::factory(5)->create();

        $response = $this->actingAs($user)->getJson('/api/movies');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'title',
                        'director',
                        'status',
                    ],
                ],
                'meta' => [
                    'current_page',
                    'last_page',
                    'per_page',
                    'total',
                ],
            ]);
    }

    public function test_can_create_movie(): void
    {
        $user = User::factory()->create();

        $data = [
            'title' => 'Test Movie',
            'director' => 'Test Director',
            'description' => 'Test description',
            'status' => 'available',
        ];

        $response = $this->actingAs($user)->postJson('/api/movies', $data);

        $response->assertStatus(201)
            ->assertJson([
                'message' => 'Movie created successfully',
                'data' => [
                    'title' => 'Test Movie',
                    'director' => 'Test Director',
                ],
            ]);

        $this->assertDatabaseHas('movies', ['title' => 'Test Movie']);
    }

    public function test_can_create_movie_with_cover(): void
    {
        $user = User::factory()->create();
        Storage::fake('public');

        $cover = UploadedFile::fake()->image('cover.jpg');

        $data = [
            'title' => 'Test Movie',
            'director' => 'Test Director',
            'status' => 'available',
            'cover' => $cover,
        ];

        $response = $this->actingAs($user)->postJson('/api/movies', $data);

        $response->assertStatus(201);

        $movie = Movie::first();
        $this->assertNotNull($movie->cover);
        Storage::disk('public')->assertExists($movie->cover->file_path);
    }

    public function test_can_show_movie(): void
    {
        $user = User::factory()->create();
        $movie = Movie::factory()->create();

        $response = $this->actingAs($user)->getJson("/api/movies/{$movie->id}");

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => $movie->id,
                    'title' => $movie->title,
                    'director' => $movie->director,
                ],
            ]);
    }

    public function test_can_update_movie(): void
    {
        $user = User::factory()->create();
        $movie = Movie::factory()->create(['title' => 'Original Title']);

        $data = ['title' => 'Updated Title'];

        $response = $this->actingAs($user)->putJson("/api/movies/{$movie->id}", $data);

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Movie updated successfully',
                'data' => [
                    'title' => 'Updated Title',
                ],
            ]);

        $this->assertDatabaseHas('movies', ['title' => 'Updated Title']);
    }

    public function test_can_delete_movie(): void
    {
        $user = User::factory()->create();
        $movie = Movie::factory()->create();

        $response = $this->actingAs($user)->deleteJson("/api/movies/{$movie->id}");

        $response->assertStatus(204);

        $this->assertSoftDeleted('movies', ['id' => $movie->id]);
    }

    public function test_returns_404_for_nonexistent_movie(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->getJson('/api/movies/999999');

        $response->assertStatus(404);
    }

    public function test_validates_required_fields(): void
    {
        $user = User::factory()->create();
        $data = [];

        $response = $this->actingAs($user)->postJson('/api/movies', $data);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['title', 'director', 'status']);
    }

    public function test_can_filter_movies_by_query_params(): void
    {
        $user = User::factory()->create();
        Movie::factory()->create(['genre' => 'Action', 'status' => 'available']);
        Movie::factory()->create(['genre' => 'Drama', 'status' => 'unavailable']);

        $response = $this->actingAs($user)->getJson('/api/movies?genre=Action&status=available');

        $response->assertStatus(200);
    }
}
