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
});

it('can create movie', function (): void {
    $data = [
        'title' => 'Test Movie',
        'director' => 'Test Director',
        'genre' => 'Action',
        'status' => 'available',
    ];

    $response = $this->postJson('/api/movies', $data);

    $response->assertStatus(201)
        ->assertJson([
            'message' => 'Movie created successfully',
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
    ]);

    $response->assertStatus(200)
        ->assertJson([
            'message' => 'Movie updated successfully',
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

it('requires authentication to list movies', function (): void {
    auth()->logout();

    $response = $this->getJson('/api/movies');

    $response->assertStatus(401)
        ->assertJson(['message' => 'Unauthenticated']);
});

it('requires authentication to create movie', function (): void {
    auth()->logout();

    $response = $this->postJson('/api/movies', [
        'title' => 'Test Movie',
        'director' => 'Test Director',
        'status' => 'available',
    ]);

    $response->assertStatus(401)
        ->assertJson(['message' => 'Unauthenticated']);
});

it('requires authentication to update movie', function (): void {
    $movie = Movie::factory()->create();
    auth()->logout();

    $response = $this->putJson("/api/movies/{$movie->id}", [
        'title' => 'Updated Title',
    ]);

    $response->assertStatus(401)
        ->assertJson(['message' => 'Unauthenticated']);
});

it('requires authentication to delete movie', function (): void {
    $movie = Movie::factory()->create();
    auth()->logout();

    $response = $this->deleteJson("/api/movies/{$movie->id}");

    $response->assertStatus(401)
        ->assertJson(['message' => 'Unauthenticated']);
});
