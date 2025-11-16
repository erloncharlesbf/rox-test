<?php

use App\Models\Book;
use App\Repositories\BookRepository;
use App\Services\BookService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $repository = new BookRepository(new Book);
    $this->service = new BookService($repository);
});

it('gets all books', function (): void {
    Book::factory(3)->create();

    $books = $this->service->getAllBooks();

    $this->assertCount(3, $books);
});

it('paginates books', function (): void {
    Book::factory(20)->create();

    $result = $this->service->paginateBooks(10);

    $this->assertEquals(10, $result->count());
    $this->assertEquals(20, $result->total());
});

it('finds book', function (): void {
    $book = Book::factory()->create();

    $found = $this->service->findBook($book->id);

    $this->assertNotNull($found);
    $this->assertEquals($book->id, $found->id);
});

it('creates book without cover', function (): void {
    $data = [
        'title' => 'Test Book',
        'author' => 'Test Author',
        'isbn' => '978-3-16-148410-0',
        'status' => 'available',
    ];

    $book = $this->service->createBook($data);

    $this->assertInstanceOf(Book::class, $book);
    $this->assertEquals('Test Book', $book->title);
});

it('creates book with cover', function (): void {
    Storage::fake('public');

    $data = [
        'title' => 'Test Book',
        'author' => 'Test Author',
        'isbn' => '978-3-16-148410-0',
        'status' => 'available',
        'cover' => UploadedFile::fake()->image('cover.jpg'),
    ];

    $book = $this->service->createBook($data);

    $this->assertNotNull($book->cover);
    Storage::disk('public')->assertExists($book->cover->file_path);
});

it('updates book without cover', function (): void {
    $book = Book::factory()->create(['title' => 'Old Title']);

    $updated = $this->service->updateBook($book, ['title' => 'New Title']);

    $this->assertEquals('New Title', $updated->title);
});

it('deletes book without cover', function (): void {
    $book = Book::factory()->create();

    $result = $this->service->deleteBook($book);

    $this->assertTrue($result);
    $this->assertSoftDeleted('books', ['id' => $book->id]);
});

it('finds by isbn', function (): void {
    $book = Book::factory()->create(['isbn' => '978-3-16-148410-0']);

    $found = $this->service->findByIsbn('978-3-16-148410-0');

    $this->assertNotNull($found);
    $this->assertEquals($book->id, $found->id);
});

it('updates book with cover replacement', function (): void {
    Storage::fake('public');

    $oldCover = UploadedFile::fake()->image('old-cover.jpg');
    $book = $this->service->createBook([
        'title' => 'Test Book',
        'author' => 'Test Author',
        'isbn' => '978-3-16-148410-5',
        'status' => 'available',
        'cover' => $oldCover,
    ]);

    $newCover = UploadedFile::fake()->image('new-cover.jpg');

    $updated = $this->service->updateBook($book, [
        'title' => 'Updated Title',
        'cover' => $newCover,
    ]);

    $this->assertEquals('Updated Title', $updated->title);
    $this->assertNotNull($updated->cover);
});

it('deletes book with cover', function (): void {
    Storage::fake('public');

    $cover = UploadedFile::fake()->image('cover.jpg');
    $book = $this->service->createBook([
        'title' => 'Test Book',
        'author' => 'Test Author',
        'isbn' => '978-3-16-148410-0',
        'status' => 'available',
        'cover' => $cover,
    ]);

    $result = $this->service->deleteBook($book);

    $this->assertTrue($result);
    $this->assertSoftDeleted('books', ['id' => $book->id]);
});
