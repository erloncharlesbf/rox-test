<?php

use App\Http\Resources\Attachment\AttachmentResource;
use App\Http\Resources\Book\BookResource;
use App\Http\Resources\Movie\MovieResource;
use App\Models\Attachment;
use App\Models\Book;
use App\Models\Movie;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;

uses(RefreshDatabase::class);

it('transforms Book to BookResource', function (): void {
    $book = Book::factory()->create([
        'title' => 'Test Book',
        'author' => 'Test Author',
        'isbn' => '978-3-16-148410-0',
        'status' => 'available',
    ]);

    $resource = new BookResource($book);
    $request = Request::create('/api/books');
    $array = $resource->toArray($request);

    expect($array)->toHaveKeys([
        'id',
        'title',
        'author',
        'isbn',
        'description',
        'publisher',
        'publication_date',
        'language',
        'pages',
        'genre',
        'price',
        'status',
        'cover',
        'created_at',
        'updated_at',
    ]);
    expect($array['title'])->toBe('Test Book');
    expect($array['author'])->toBe('Test Author');
    expect($array['isbn'])->toBe('978-3-16-148410-0');
});

it('transforms Movie to MovieResource', function (): void {
    $movie = Movie::factory()->create([
        'title' => 'Test Movie',
        'director' => 'Test Director',
        'status' => 'available',
    ]);

    $resource = new MovieResource($movie);
    $request = Request::create('/api/movies');
    $array = $resource->toArray($request);

    expect($array)->toHaveKeys([
        'id',
        'title',
        'director',
        'description',
        'studio',
        'release_date',
        'language',
        'duration',
        'genre',
        'rating',
        'price',
        'status',
        'cover',
        'created_at',
        'updated_at',
    ]);
    expect($array['title'])->toBe('Test Movie');
    expect($array['director'])->toBe('Test Director');
});

it('transforms Attachment to AttachmentResource', function (): void {
    $book = Book::factory()->create();

    $attachment = Attachment::create([
        'file_name' => 'test.jpg',
        'file_path' => 'attachments/test.jpg',
        'mime_type' => 'image/jpeg',
        'file_size' => 1024,
        'type' => 'cover',
        'attachable_type' => Book::class,
        'attachable_id' => $book->id,
    ]);

    $resource = new AttachmentResource($attachment);
    $request = Request::create('/api/attachments');
    $array = $resource->toArray($request);

    expect($array)->toHaveKeys([
        'id',
        'file_name',
        'file_url',
        'mime_type',
        'file_size',
        'type',
        'created_at',
    ]);
    expect($array['file_name'])->toBe('test.jpg');
    expect($array['mime_type'])->toBe('image/jpeg');
    expect($array['file_size'])->toBe(1024);
    expect($array['file_url'])->toContain('/api/attachments/');
});

it('BookResource includes attachment when cover is loaded', function (): void {
    $book = Book::factory()->create();

    $attachment = Attachment::create([
        'file_name' => 'cover.jpg',
        'file_path' => 'attachments/cover.jpg',
        'mime_type' => 'image/jpeg',
        'file_size' => 2048,
        'type' => 'cover',
        'attachable_type' => Book::class,
        'attachable_id' => $book->id,
    ]);

    $book->load('cover');

    $resource = new BookResource($book);
    $request = Request::create('/api/books');
    $array = $resource->toArray($request);

    expect($array['cover'])->not->toBeNull();
    expect($array['cover'])->toHaveKey('file_name');
    expect($array['cover']['file_name'])->toBe('cover.jpg');
});

it('MovieResource includes attachment when cover is loaded', function (): void {
    $movie = Movie::factory()->create();

    $attachment = Attachment::create([
        'file_name' => 'poster.jpg',
        'file_path' => 'attachments/poster.jpg',
        'mime_type' => 'image/jpeg',
        'file_size' => 3072,
        'type' => 'cover',
        'attachable_type' => Movie::class,
        'attachable_id' => $movie->id,
    ]);

    $movie->load('cover');

    $resource = new MovieResource($movie);
    $request = Request::create('/api/movies');
    $array = $resource->toArray($request);

    expect($array['cover'])->not->toBeNull();
    expect($array['cover'])->toHaveKey('file_name');
    expect($array['cover']['file_name'])->toBe('poster.jpg');
});
