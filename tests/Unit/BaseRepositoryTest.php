<?php

use App\Models\Movie;
use App\Repositories\MovieRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->repository = new MovieRepository(new Movie);
});

it('all method returns collection', function (): void {
    Movie::factory(3)->create();

    $movies = $this->repository->all();

    $this->assertCount(3, $movies);
});

it('paginates with filters array value', function (): void {
    Movie::factory()->create(['status' => 'available']);
    Movie::factory()->create(['status' => 'unavailable']);

    $result = $this->repository->paginate(15, ['status' => ['available']]);

    $this->assertEquals(1, $result->total());
});

it('find returns null for nonexistent id', function (): void {
    $result = $this->repository->find(999999);

    $this->assertNull($result);
});

it('update returns false for nonexistent id', function (): void {
    $result = $this->repository->update(999999, ['title' => 'Test']);

    $this->assertFalse($result);
});

it('delete returns false for nonexistent id', function (): void {
    $result = $this->repository->delete(999999);

    $this->assertFalse($result);
});

it('uses find by method', function (): void {
    $movie = Movie::factory()->create(['title' => 'Unique Title']);

    $found = $this->repository->findBy('title', 'Unique Title');

    $this->assertNotNull($found);
    $this->assertEquals('Unique Title', $found->title);
});

it('uses find where method', function (): void {
    Movie::factory()->create(['genre' => 'Action', 'status' => 'available']);
    Movie::factory()->create(['genre' => 'Action', 'status' => 'unavailable']);
    Movie::factory()->create(['genre' => 'Drama', 'status' => 'available']);

    $results = $this->repository->findWhere(['genre' => 'Action', 'status' => 'available']);

    $this->assertCount(1, $results);
});
