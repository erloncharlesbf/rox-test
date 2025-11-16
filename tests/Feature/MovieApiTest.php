<?php

use App\Models\Movie;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses()->group('movies', 'api');

beforeEach(function (): void {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
});

it('can list movies', function (): void {
    Movie::factory(3)->create();

    $response = $this->getJson('/api/movies');

    $response->assertStatus(200)
        ->assertJsonCount(3, 'data');
});

it('can create movie', function (): void {
    $data = [
        'title' => 'Test Movie',
        'director' => 'Test Director',
        'genre' => 'Action',
        'release_year' => 2024,
        'status' => 'available',
    ];

    $response = $this->postJson('/api/movies', $data);

    $response->assertStatus(201)
        ->assertJson([
            'data' => [
                'title' => 'Test Movie',
                'director' => 'Test Director',
            ],
        ]);

    $this->assertDatabaseHas('movies', ['title' => 'Test Movie']);
});

it('can create movie with cover', function (): void {
    Storage::fake('public');

    $data = [
        'title' => 'Test Movie',
        'director' => 'Test Director',
        'status' => 'available',
        'cover' => UploadedFile::fake()->image('cover.jpg'),
    ];

    $response = $this->postJson('/api/movies', $data);

    $response->assertStatus(201);
    expect($response->json('data.cover'))->not->toBeNull();
});

it('can show movie', function (): void {
    $movie = Movie::factory()->create();

    $response = $this->getJson("/api/movies/{$movie->id}");

    $response->assertStatus(200)
        ->assertJson([
            'data' => [
                'id' => $movie->id,
                'title' => $movie->title,
            ],
        ]);
});

it('can update movie', function (): void {
    $movie = Movie::factory()->create(['title' => 'Original Title']);

    $response = $this->putJson("/api/movies/{$movie->id}", [
        'title' => 'Updated Title',
        'director' => $movie->director,
        'status' => $movie->status,
    ]);

    $response->assertStatus(200)
        ->assertJson([
            'data' => [
                'title' => 'Updated Title',
            ],
        ]);

    $this->assertDatabaseHas('movies', ['title' => 'Updated Title']);
});

it('can delete movie', function (): void {
    $movie = Movie::factory()->create();

    $response = $this->deleteJson("/api/movies/{$movie->id}");

    $response->assertStatus(204);
    $this->assertSoftDeleted('movies', ['id' => $movie->id]);
});

it('returns 404 for nonexistent movie', function (): void {
    $response = $this->getJson('/api/movies/99999');

    $response->assertStatus(404);
});

it('validates required fields', function (): void {
    $response = $this->postJson('/api/movies', []);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['title', 'director', 'status']);
});

it('can filter movies by query params', function (): void {
    Movie::factory()->create(['genre' => 'Action', 'status' => 'available']);
    Movie::factory()->create(['genre' => 'Drama', 'status' => 'unavailable']);

    $response = $this->getJson('/api/movies?genre=Action&status=available');

    $response->assertStatus(200);
});
