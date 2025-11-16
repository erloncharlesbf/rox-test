<?php

use App\Models\Movie;
use App\Repositories\MovieRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->repository = new MovieRepository(new Movie);
});

it('can create movie', function (): void {
    $data = [
        'title' => 'Test Movie',
        'director' => 'Test Director',
        'status' => 'available',
    ];

    $movie = $this->repository->create($data);

    $this->assertInstanceOf(Movie::class, $movie);
    $this->assertEquals('Test Movie', $movie->title);
    $this->assertDatabaseHas('movies', ['title' => 'Test Movie']);
});

it('can find movie by id', function (): void {
    $movie = Movie::factory()->create();

    $found = $this->repository->find($movie->id);

    $this->assertInstanceOf(Movie::class, $found);
    $this->assertEquals($movie->id, $found->id);
});

it('can update movie', function (): void {
    $movie = Movie::factory()->create(['title' => 'Original Title']);

    $updated = $this->repository->update($movie->id, ['title' => 'Updated Title']);

    $this->assertTrue($updated);
    $this->assertDatabaseHas('movies', ['title' => 'Updated Title']);
});

it('can delete movie', function (): void {
    $movie = Movie::factory()->create();

    $deleted = $this->repository->delete($movie->id);

    $this->assertTrue($deleted);
    $this->assertSoftDeleted('movies', ['id' => $movie->id]);
});

it('can filter movies by genre', function (): void {
    Movie::factory()->create(['genre' => 'Action']);
    Movie::factory()->create(['genre' => 'Action']);
    Movie::factory()->create(['genre' => 'Drama']);

    $movies = $this->repository->filterByGenre('Action');

    $this->assertCount(2, $movies);
});

it('can filter movies by director', function (): void {
    Movie::factory()->create(['director' => 'Steven Spielberg']);
    Movie::factory()->create(['director' => 'Steven Stone']);
    Movie::factory()->create(['director' => 'Christopher Nolan']);

    $movies = $this->repository->filterByDirector('Steven');

    $this->assertCount(2, $movies);
});

it('can paginate movies', function (): void {
    Movie::factory(25)->create();

    $paginated = $this->repository->paginate(10);

    $this->assertEquals(10, $paginated->count());
    $this->assertEquals(25, $paginated->total());
});

it('can filter movies by status', function (): void {
    Movie::factory()->create(['status' => 'available']);
    Movie::factory()->create(['status' => 'available']);
    Movie::factory()->create(['status' => 'unavailable']);

    $movies = $this->repository->filterByStatus('available');

    $this->assertCount(2, $movies);
});

it('can filter movies by rating', function (): void {
    Movie::factory()->create(['rating' => 8.5]);
    Movie::factory()->create(['rating' => 9.0]);
    Movie::factory()->create(['rating' => 7.0]);

    $movies = $this->repository->filterByRating(8.0);

    $this->assertCount(2, $movies);
});
