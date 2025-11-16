<?php

use App\Models\Book;
use App\Repositories\BookRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->repository = new BookRepository(new Book);
});

it('can create book', function (): void {
    $data = [
        'title' => 'Test Book',
        'author' => 'Test Author',
        'isbn' => '978-3-16-148410-0',
        'status' => 'available',
    ];

    $book = $this->repository->create($data);

    $this->assertInstanceOf(Book::class, $book);
    $this->assertEquals('Test Book', $book->title);
    $this->assertDatabaseHas('books', ['title' => 'Test Book']);
});

it('can find book by id', function (): void {
    $book = Book::factory()->create();

    $found = $this->repository->find($book->id);

    $this->assertInstanceOf(Book::class, $found);
    $this->assertEquals($book->id, $found->id);
});

it('can find book by isbn', function (): void {
    $book = Book::factory()->create(['isbn' => '978-3-16-148410-0']);

    $found = $this->repository->findByIsbn('978-3-16-148410-0');

    $this->assertInstanceOf(Book::class, $found);
    $this->assertEquals($book->isbn, $found->isbn);
});

it('can update book', function (): void {
    $book = Book::factory()->create(['title' => 'Original Title']);

    $updated = $this->repository->update($book->id, ['title' => 'Updated Title']);

    $this->assertTrue($updated);
    $this->assertDatabaseHas('books', ['title' => 'Updated Title']);
});

it('can delete book', function (): void {
    $book = Book::factory()->create();

    $deleted = $this->repository->delete($book->id);

    $this->assertTrue($deleted);
    $this->assertSoftDeleted('books', ['id' => $book->id]);
});

it('can filter books by genre', function (): void {
    Book::factory()->create(['genre' => 'Fiction']);
    Book::factory()->create(['genre' => 'Fiction']);
    Book::factory()->create(['genre' => 'Science']);

    $books = $this->repository->filterByGenre('Fiction');

    $this->assertCount(2, $books);
});

it('can filter books by author', function (): void {
    Book::factory()->create(['author' => 'John Doe']);
    Book::factory()->create(['author' => 'John Smith']);
    Book::factory()->create(['author' => 'Jane Doe']);

    $books = $this->repository->filterByAuthor('John');

    $this->assertCount(2, $books);
});

it('can paginate books', function (): void {
    Book::factory(30)->create();

    $paginated = $this->repository->paginate(15);

    $this->assertEquals(15, $paginated->count());
    $this->assertEquals(30, $paginated->total());
});

it('can filter books by status', function (): void {
    Book::factory()->create(['status' => 'available']);
    Book::factory()->create(['status' => 'available']);
    Book::factory()->create(['status' => 'unavailable']);

    $books = $this->repository->filterByStatus('available');

    $this->assertCount(2, $books);
});
