<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class BookApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_list_books(): void
    {
        $user = User::factory()->create();
        Book::factory(5)->create();

        $response = $this->actingAs($user)->getJson('/api/books');

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
    }

    public function test_can_create_book(): void
    {
        $user = User::factory()->create();

        $data = [
            'title' => 'Test Book',
            'author' => 'Test Author',
            'isbn' => '978-3-16-148410-0',
            'description' => 'Test description',
            'status' => 'available',
        ];

        $response = $this->actingAs($user)->postJson('/api/books', $data);

        $response->assertStatus(201)
            ->assertJson([
                'message' => 'Book created successfully',
                'data' => [
                    'title' => 'Test Book',
                    'author' => 'Test Author',
                ],
            ]);

        $this->assertDatabaseHas('books', ['title' => 'Test Book']);
    }

    public function test_can_create_book_with_cover(): void
    {
        $user = User::factory()->create();
        Storage::fake('public');

        $cover = UploadedFile::fake()->image('cover.jpg');

        $data = [
            'title' => 'Test Book',
            'author' => 'Test Author',
            'isbn' => '978-3-16-148410-0',
            'status' => 'available',
            'cover' => $cover,
        ];

        $response = $this->actingAs($user)->postJson('/api/books', $data);

        $response->assertStatus(201);

        $book = Book::first();
        $this->assertNotNull($book->cover);
        Storage::disk('public')->assertExists($book->cover->file_path);
    }

    public function test_cannot_create_book_with_duplicate_isbn(): void
    {
        $user = User::factory()->create();
        Book::factory()->create(['isbn' => '978-3-16-148410-0']);

        $data = [
            'title' => 'Test Book',
            'author' => 'Test Author',
            'isbn' => '978-3-16-148410-0',
            'status' => 'available',
        ];

        $response = $this->actingAs($user)->postJson('/api/books', $data);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('isbn');
    }

    public function test_can_show_book(): void
    {
        $user = User::factory()->create();
        $book = Book::factory()->create();

        $response = $this->actingAs($user)->getJson("/api/books/{$book->id}");

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => $book->id,
                    'title' => $book->title,
                    'author' => $book->author,
                ],
            ]);
    }

    public function test_can_update_book(): void
    {
        $user = User::factory()->create();
        $book = Book::factory()->create(['title' => 'Original Title']);

        $data = ['title' => 'Updated Title'];

        $response = $this->actingAs($user)->putJson("/api/books/{$book->id}", $data);

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Book updated successfully',
                'data' => [
                    'title' => 'Updated Title',
                ],
            ]);

        $this->assertDatabaseHas('books', ['title' => 'Updated Title']);
    }

    public function test_can_delete_book(): void
    {
        $user = User::factory()->create();
        $book = Book::factory()->create();

        $response = $this->actingAs($user)->deleteJson("/api/books/{$book->id}");

        $response->assertStatus(204);

        $this->assertSoftDeleted('books', ['id' => $book->id]);
    }

    public function test_returns_404_for_nonexistent_book(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->getJson('/api/books/999999');

        $response->assertStatus(404);
    }

    public function test_validates_required_fields(): void
    {
        $user = User::factory()->create();
        $data = [];

        $response = $this->actingAs($user)->postJson('/api/books', $data);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['title', 'author', 'isbn', 'status']);
    }

    public function test_can_filter_books_by_query_params(): void
    {
        $user = User::factory()->create();
        Book::factory()->create(['genre' => 'Fiction', 'status' => 'available']);
        Book::factory()->create(['genre' => 'Science', 'status' => 'unavailable']);

        $response = $this->actingAs($user)->getJson('/api/books?genre=Fiction&status=available');

        $response->assertStatus(200);
    }
}
