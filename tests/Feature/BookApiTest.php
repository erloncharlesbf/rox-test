<?php

use App\Models\Book;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses()->group('books', 'api');

beforeEach(function (): void {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
});

it('can list books', function (): void {
    Book::factory(5)->create();

    $response = $this->getJson('/api/books');

    $response->assertStatus(200)
        ->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'title',
                    'author',
                    'isbn',
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

it('can create book', function (): void {
    $data = [
        'title' => 'Test Book',
        'author' => 'Test Author',
        'isbn' => '978-3-16-148410-0',
        'description' => 'Test description',
        'status' => 'available',
    ];

    $response = $this->postJson('/api/books', $data);

    $response->assertStatus(201)
        ->assertJson([
            'message' => 'Book created successfully',
            'data' => [
                'title' => 'Test Book',
                'author' => 'Test Author',
            ],
        ]);

    $this->assertDatabaseHas('books', ['title' => 'Test Book']);
});

it('can create book with cover', function (): void {
    Storage::fake('public');

    $cover = UploadedFile::fake()->image('cover.jpg');

    $data = [
        'title' => 'Test Book',
        'author' => 'Test Author',
        'isbn' => '978-3-16-148410-0',
        'status' => 'available',
        'cover' => $cover,
    ];

    $response = $this->postJson('/api/books', $data);

    $response->assertStatus(201);

    $book = Book::query()->first();
    $this->assertNotNull($book->cover);
    Storage::disk('public')->assertExists($book->cover->file_path);
});

it('cannot create book with duplicate isbn', function (): void {
    Book::factory()->create(['isbn' => '978-3-16-148410-0']);

    $data = [
        'title' => 'Test Book',
        'author' => 'Test Author',
        'isbn' => '978-3-16-148410-0',
        'status' => 'available',
    ];

    $response = $this->postJson('/api/books', $data);

    $response->assertStatus(422)
        ->assertJsonValidationErrors('isbn');
});

it('can show book', function (): void {
    $book = Book::factory()->create();

    $response = $this->getJson("/api/books/{$book->id}");

    $response->assertStatus(200)
        ->assertJson([
            'data' => [
                'id' => $book->id,
                'title' => $book->title,
                'author' => $book->author,
            ],
        ]);
});

it('can update book', function (): void {
    $book = Book::factory()->create(['title' => 'Original Title']);

    $data = ['title' => 'Updated Title'];

    $response = $this->putJson("/api/books/{$book->id}", $data);

    $response->assertStatus(200)
        ->assertJson([
            'message' => 'Book updated successfully',
            'data' => [
                'title' => 'Updated Title',
            ],
        ]);

    $this->assertDatabaseHas('books', ['title' => 'Updated Title']);
});

it('can delete book', function (): void {
    $book = Book::factory()->create();

    $response = $this->deleteJson("/api/books/{$book->id}");

    $response->assertStatus(204);

    $this->assertSoftDeleted('books', ['id' => $book->id]);
});

it('returns 404 for nonexistent book', function (): void {
    $response = $this->getJson('/api/books/999999');

    $response->assertStatus(404);
});

it('validates required fields', function (): void {
    $data = [];

    $response = $this->postJson('/api/books', $data);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['title', 'author', 'isbn', 'status']);
});

it('can filter books by query params', function (): void {
    Book::factory()->create(['genre' => 'Fiction', 'status' => 'available']);
    Book::factory()->create(['genre' => 'Science', 'status' => 'unavailable']);

    $response = $this->getJson('/api/books?genre=Fiction&status=available');

    $response->assertStatus(200);
});

it('requires authentication to list books', function (): void {
    auth()->logout();

    $response = $this->getJson('/api/books');

    $response->assertStatus(401)
        ->assertJson(['message' => 'Unauthenticated']);
});

it('requires authentication to create book', function (): void {
    auth()->logout();

    $response = $this->postJson('/api/books', [
        'title' => 'Test Book',
        'author' => 'Test Author',
        'isbn' => '978-3-16-148410-0',
        'status' => 'available',
    ]);

    $response->assertStatus(401)
        ->assertJson(['message' => 'Unauthenticated']);
});

it('requires authentication to update book', function (): void {
    $book = Book::factory()->create();
    auth()->logout();

    $response = $this->putJson("/api/books/{$book->id}", [
        'title' => 'Updated Title',
    ]);

    $response->assertStatus(401)
        ->assertJson(['message' => 'Unauthenticated']);
});

it('requires authentication to delete book', function (): void {
    $book = Book::factory()->create();
    auth()->logout();

    $response = $this->deleteJson("/api/books/{$book->id}");

    $response->assertStatus(401)
        ->assertJson(['message' => 'Unauthenticated']);
});
