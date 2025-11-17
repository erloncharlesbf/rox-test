<?php

use App\Http\Requests\Book\DestroyBookRequest;
use App\Http\Requests\Book\IndexBookRequest;
use App\Http\Requests\Book\ShowBookRequest;
use App\Http\Requests\Book\StoreBookRequest;
use App\Http\Requests\Book\UpdateBookRequest;
use App\Http\Requests\Movie\DestroyMovieRequest;
use App\Http\Requests\Movie\IndexMovieRequest;
use App\Http\Requests\Movie\ShowMovieRequest;
use App\Http\Requests\Movie\StoreMovieRequest;
use App\Http\Requests\Movie\UpdateMovieRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
});

// Book Requests Tests
it('IndexBookRequest authorizes authenticated users', function (): void {
    $request = new IndexBookRequest;

    expect($request->authorize())->toBeTrue();
});

it('IndexBookRequest validates query parameters', function (): void {
    $request = new IndexBookRequest;

    $validator = Validator::make([
        'page' => 1,
        'per_page' => 50,
        'title' => 'Test',
        'author' => 'Author',
        'genre' => 'Fiction',
        'status' => 'available',
    ], $request->rules());

    expect($validator->passes())->toBeTrue();
});

it('IndexBookRequest fails with invalid per_page', function (): void {
    $request = new IndexBookRequest;

    $validator = Validator::make([
        'per_page' => 150, // max is 100
    ], $request->rules());

    expect($validator->fails())->toBeTrue();
    expect($validator->errors()->has('per_page'))->toBeTrue();
});

it('ShowBookRequest authorizes authenticated users', function (): void {
    $request = new ShowBookRequest;

    expect($request->authorize())->toBeTrue();
});

it('DestroyBookRequest authorizes authenticated users', function (): void {
    $request = new DestroyBookRequest;

    expect($request->authorize())->toBeTrue();
});

it('StoreBookRequest validates required fields', function (): void {
    $request = new StoreBookRequest;

    $validator = Validator::make([
        'title' => 'Test Book',
        'author' => 'Test Author',
        'isbn' => '978-3-16-148410-0',
        'status' => 'available',
    ], $request->rules());

    expect($validator->passes())->toBeTrue();
});

it('StoreBookRequest fails without required fields', function (): void {
    $request = new StoreBookRequest;

    $validator = Validator::make([], $request->rules());

    expect($validator->fails())->toBeTrue();
    expect($validator->errors()->has('title'))->toBeTrue();
    expect($validator->errors()->has('author'))->toBeTrue();
    expect($validator->errors()->has('isbn'))->toBeTrue();
    expect($validator->errors()->has('status'))->toBeTrue();
});

it('UpdateBookRequest validates optional fields', function (): void {
    $request = new UpdateBookRequest;

    $validator = Validator::make([
        'title' => 'Updated Title',
    ], $request->rules());

    expect($validator->passes())->toBeTrue();
});

// Movie Requests Tests
it('IndexMovieRequest authorizes authenticated users', function (): void {
    $request = new IndexMovieRequest;

    expect($request->authorize())->toBeTrue();
});

it('IndexMovieRequest validates query parameters', function (): void {
    $request = new IndexMovieRequest;

    $validator = Validator::make([
        'page' => 1,
        'per_page' => 50,
        'title' => 'Test',
        'director' => 'Director',
        'genre' => 'Action',
        'status' => 'available',
    ], $request->rules());

    expect($validator->passes())->toBeTrue();
});

it('ShowMovieRequest authorizes authenticated users', function (): void {
    $request = new ShowMovieRequest;

    expect($request->authorize())->toBeTrue();
});

it('DestroyMovieRequest authorizes authenticated users', function (): void {
    $request = new DestroyMovieRequest;

    expect($request->authorize())->toBeTrue();
});

it('StoreMovieRequest validates required fields', function (): void {
    $request = new StoreMovieRequest;

    $validator = Validator::make([
        'title' => 'Test Movie',
        'director' => 'Test Director',
        'status' => 'available',
    ], $request->rules());

    expect($validator->passes())->toBeTrue();
});

it('StoreMovieRequest fails without required fields', function (): void {
    $request = new StoreMovieRequest;

    $validator = Validator::make([], $request->rules());

    expect($validator->fails())->toBeTrue();
    expect($validator->errors()->has('title'))->toBeTrue();
    expect($validator->errors()->has('director'))->toBeTrue();
    expect($validator->errors()->has('status'))->toBeTrue();
});

it('UpdateMovieRequest validates optional fields', function (): void {
    $request = new UpdateMovieRequest;

    $validator = Validator::make([
        'title' => 'Updated Title',
    ], $request->rules());

    expect($validator->passes())->toBeTrue();
});

// Unauthorized tests
it('requests fail authorization when not authenticated', function (): void {
    auth()->logout();

    $indexBookRequest = new IndexBookRequest;
    $showBookRequest = new ShowBookRequest;
    $destroyBookRequest = new DestroyBookRequest;
    $indexMovieRequest = new IndexMovieRequest;
    $showMovieRequest = new ShowMovieRequest;
    $destroyMovieRequest = new DestroyMovieRequest;

    expect($indexBookRequest->authorize())->toBeFalse();
    expect($showBookRequest->authorize())->toBeFalse();
    expect($destroyBookRequest->authorize())->toBeFalse();
    expect($indexMovieRequest->authorize())->toBeFalse();
    expect($showMovieRequest->authorize())->toBeFalse();
    expect($destroyMovieRequest->authorize())->toBeFalse();
});
