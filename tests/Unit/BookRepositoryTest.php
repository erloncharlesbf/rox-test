<?php

namespace Tests\Unit;

use App\Models\Book;
use App\Repositories\BookRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookRepositoryTest extends TestCase
{
    use RefreshDatabase;

    private BookRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new BookRepository(new Book);
    }

    public function test_can_create_book(): void
    {
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
    }

    public function test_can_find_book_by_id(): void
    {
        $book = Book::factory()->create();

        $found = $this->repository->find($book->id);

        $this->assertInstanceOf(Book::class, $found);
        $this->assertEquals($book->id, $found->id);
    }

    public function test_can_find_book_by_isbn(): void
    {
        $book = Book::factory()->create(['isbn' => '978-3-16-148410-0']);

        $found = $this->repository->findByIsbn('978-3-16-148410-0');

        $this->assertInstanceOf(Book::class, $found);
        $this->assertEquals($book->isbn, $found->isbn);
    }

    public function test_can_update_book(): void
    {
        $book = Book::factory()->create(['title' => 'Original Title']);

        $updated = $this->repository->update($book->id, ['title' => 'Updated Title']);

        $this->assertTrue($updated);
        $this->assertDatabaseHas('books', ['title' => 'Updated Title']);
    }

    public function test_can_delete_book(): void
    {
        $book = Book::factory()->create();

        $deleted = $this->repository->delete($book->id);

        $this->assertTrue($deleted);
        $this->assertSoftDeleted('books', ['id' => $book->id]);
    }

    public function test_can_filter_books_by_genre(): void
    {
        Book::factory()->create(['genre' => 'Fiction']);
        Book::factory()->create(['genre' => 'Fiction']);
        Book::factory()->create(['genre' => 'Science']);

        $books = $this->repository->filterByGenre('Fiction');

        $this->assertCount(2, $books);
    }

    public function test_can_filter_books_by_author(): void
    {
        Book::factory()->create(['author' => 'John Doe']);
        Book::factory()->create(['author' => 'John Smith']);
        Book::factory()->create(['author' => 'Jane Doe']);

        $books = $this->repository->filterByAuthor('John');

        $this->assertCount(2, $books);
    }

    public function test_can_paginate_books(): void
    {
        Book::factory(30)->create();

        $paginated = $this->repository->paginate(15);

        $this->assertEquals(15, $paginated->count());
        $this->assertEquals(30, $paginated->total());
    }
}
