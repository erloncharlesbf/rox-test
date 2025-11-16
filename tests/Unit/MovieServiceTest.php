<?php

use App\Models\Movie;
use App\Repositories\MovieRepository;
use App\Services\MovieService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $repository = new MovieRepository(new Movie);
    $this->service = new MovieService($repository);
});

it('gets all movies', function (): void {
    Movie::factory(3)->create();

    $movies = $this->service->getAllMovies();

    $this->assertCount(3, $movies);
});

it('paginates movies', function (): void {
    Movie::factory(20)->create();

    $result = $this->service->paginateMovies(10);

    $this->assertEquals(10, $result->count());
    $this->assertEquals(20, $result->total());
});

it('finds movie', function (): void {
    $movie = Movie::factory()->create();

    $found = $this->service->findMovie($movie->id);

    $this->assertNotNull($found);
    $this->assertEquals($movie->id, $found->id);
});

it('creates movie without cover', function (): void {
    $data = [
        'title' => 'Test Movie',
        'director' => 'Test Director',
        'status' => 'available',
    ];

    $movie = $this->service->createMovie($data);

    $this->assertInstanceOf(Movie::class, $movie);
    $this->assertEquals('Test Movie', $movie->title);
});

it('creates movie with cover', function (): void {
    Storage::fake('public');

    $data = [
        'title' => 'Test Movie',
        'director' => 'Test Director',
        'status' => 'available',
        'cover' => UploadedFile::fake()->image('cover.jpg'),
    ];

    $movie = $this->service->createMovie($data);

    $this->assertNotNull($movie->cover);
    Storage::disk('public')->assertExists($movie->cover->file_path);
});

it('updates movie without cover', function (): void {
    $movie = Movie::factory()->create(['title' => 'Old Title']);

    $updated = $this->service->updateMovie($movie, ['title' => 'New Title']);

    $this->assertEquals('New Title', $updated->title);
});

it('deletes movie without cover', function (): void {
    $movie = Movie::factory()->create();

    $result = $this->service->deleteMovie($movie);

    $this->assertTrue($result);
    $this->assertSoftDeleted('movies', ['id' => $movie->id]);
});

it('updates movie with cover replacement', function (): void {
    Storage::fake('public');

    $oldCover = UploadedFile::fake()->image('old-cover.jpg');
    $movie = $this->service->createMovie([
        'title' => 'Test Movie',
        'director' => 'Test Director',
        'status' => 'available',
        'cover' => $oldCover,
    ]);

    $newCover = UploadedFile::fake()->image('new-cover.jpg');

    $updated = $this->service->updateMovie($movie, [
        'title' => 'Updated Title',
        'cover' => $newCover,
    ]);

    $this->assertEquals('Updated Title', $updated->title);
    $this->assertNotNull($updated->cover);
});

it('deletes movie with cover', function (): void {
    Storage::fake('public');

    $cover = UploadedFile::fake()->image('cover.jpg');
    $movie = $this->service->createMovie([
        'title' => 'Test Movie',
        'director' => 'Test Director',
        'status' => 'available',
        'cover' => $cover,
    ]);

    $result = $this->service->deleteMovie($movie);

    $this->assertTrue($result);
    $this->assertSoftDeleted('movies', ['id' => $movie->id]);
});
