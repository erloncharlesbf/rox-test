<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Book\DestroyBookRequest;
use App\Http\Requests\Book\IndexBookRequest;
use App\Http\Requests\Book\ShowBookRequest;
use App\Http\Requests\Book\StoreBookRequest;
use App\Http\Requests\Book\UpdateBookRequest;
use App\Http\Resources\Book\BookResource;
use App\Models\Book;
use App\Services\BookService;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\JsonResponse;
use Knuckles\Scribe\Attributes\Authenticated;
use Knuckles\Scribe\Attributes\BodyParam;
use Knuckles\Scribe\Attributes\Endpoint;
use Knuckles\Scribe\Attributes\Group;
use Knuckles\Scribe\Attributes\QueryParam;
use Knuckles\Scribe\Attributes\ResponseFromApiResource;

#[Group(
    'Books Management',
    'APIs for managing books in your digital library. All endpoints support full CRUD operations with advanced filtering and pagination.'
)]
#[Authenticated]
class BookController extends Controller
{
    public function __construct(
        private readonly BookService $bookService,
        private readonly ResponseFactory $responseFactory
    ) {}

    #[Endpoint(
        title: 'List all books',
        description: 'Retrieve a paginated list of all books in the library. Supports filtering by multiple criteria and customizable pagination.'
    )]
    #[QueryParam('page', 'integer', 'Page number for pagination', example: 1)]
    #[QueryParam('per_page', 'integer', 'Number of items per page (default: 15, max: 100)', example: 15)]
    #[QueryParam('title', 'string', 'Filter books by title (partial match)', required: false, example: 'Gatsby')]
    #[QueryParam('author', 'string', 'Filter books by author name (partial match)', required: false, example: 'Fitzgerald')]
    #[QueryParam('genre', 'string', 'Filter books by genre (exact match)', required: false, example: 'Fiction')]
    #[QueryParam('status', 'string', 'Filter books by availability status (available, unavailable, coming_soon)', required: false, example: 'available')]
    #[ResponseFromApiResource(BookResource::class, Book::class, collection: true, paginate: 15)]
    public function index(IndexBookRequest $request): JsonResponse
    {
        $perPage = (int) $request->input('per_page', 15);
        $filters = $request->only(['title', 'author', 'genre', 'status']);

        $books = $this->bookService->paginateBooks($perPage, $filters);

        return $this->responseFactory->json([
            'data' => BookResource::collection($books->load('cover')),
            'meta' => [
                'current_page' => $books->currentPage(),
                'last_page' => $books->lastPage(),
                'per_page' => $books->perPage(),
                'total' => $books->total(),
            ],
        ]);
    }

    #[Endpoint(
        title: 'Create a new book',
        description: 'Add a new book to the library with all its details. Optionally upload a cover image (max 2MB, formats: jpeg, png, jpg, gif, webp).'
    )]
    #[BodyParam('title', 'string', 'The title of the book', required: true, example: 'The Great Gatsby')]
    #[BodyParam('author', 'string', 'The name of the book author', required: true, example: 'F. Scott Fitzgerald')]
    #[BodyParam('isbn', 'string', 'Unique ISBN-13 identifier for the book', required: true, example: '978-0-7432-7356-5')]
    #[BodyParam('description', 'string', 'A brief description or synopsis of the book', required: false, example: 'A story of decadence and excess...')]
    #[BodyParam('publisher', 'string', 'The name of the publisher', required: false, example: 'Scribner')]
    #[BodyParam('publication_date', 'string', 'Date when the book was published (YYYY-MM-DD)', required: false, example: '1925-04-10')]
    #[BodyParam('language', 'string', 'Two-letter ISO language code', required: false, example: 'en')]
    #[BodyParam('pages', 'integer', 'Total number of pages in the book', required: false, example: 180)]
    #[BodyParam('genre', 'string', 'The literary genre or category', required: false, example: 'Fiction')]
    #[BodyParam('price', 'number', 'The price of the book in USD', required: false, example: 15.99)]
    #[BodyParam('status', 'string', 'Current availability status: available, unavailable, or coming_soon', required: true, example: 'available')]
    #[BodyParam('cover', 'file', 'Cover image file (max 2MB, jpeg/png/jpg/gif/webp)', required: false)]
    #[ResponseFromApiResource(BookResource::class, Book::class, status: 201)]
    public function store(StoreBookRequest $request): JsonResponse
    {
        $book = $this->bookService->createBook($request->validated());

        return $this->responseFactory->json([
            'message' => 'Book created successfully',
            'data' => BookResource::make($book),
        ], 201);
    }

    #[Endpoint(
        title: 'Get book details',
        description: 'Retrieve detailed information about a specific book by its ID, including cover image if available.'
    )]
    #[ResponseFromApiResource(BookResource::class, Book::class)]
    public function show(ShowBookRequest $request, Book $book): JsonResponse
    {
        return $this->responseFactory->json([
            'data' => BookResource::make($book->load('cover')),
        ]);
    }

    #[Endpoint(
        title: 'Update book information',
        description: 'Update one or more fields of an existing book. All fields are optional. If a new cover image is provided, the old one will be replaced.'
    )]
    #[BodyParam('title', 'string', 'The title of the book', required: false, example: 'The Great Gatsby')]
    #[BodyParam('author', 'string', 'The name of the book author', required: false, example: 'F. Scott Fitzgerald')]
    #[BodyParam('isbn', 'string', 'Unique ISBN-13 identifier (must be unique)', required: false, example: '978-0-7432-7356-5')]
    #[BodyParam('description', 'string', 'A brief description or synopsis of the book', required: false)]
    #[BodyParam('publisher', 'string', 'The name of the publisher', required: false, example: 'Scribner')]
    #[BodyParam('publication_date', 'string', 'Date when the book was published (YYYY-MM-DD)', required: false, example: '1925-04-10')]
    #[BodyParam('language', 'string', 'Two-letter ISO language code', required: false, example: 'en')]
    #[BodyParam('pages', 'integer', 'Total number of pages in the book', required: false, example: 180)]
    #[BodyParam('genre', 'string', 'The literary genre or category', required: false, example: 'Fiction')]
    #[BodyParam('price', 'number', 'The price of the book in USD', required: false, example: 15.99)]
    #[BodyParam('status', 'string', 'Current availability status', required: false, example: 'available')]
    #[BodyParam('cover', 'file', 'New cover image file (replaces existing)', required: false)]
    #[ResponseFromApiResource(BookResource::class, Book::class)]
    public function update(UpdateBookRequest $request, Book $book): JsonResponse
    {
        $updatedBook = $this->bookService->updateBook($book, $request->validated());

        return $this->responseFactory->json([
            'message' => 'Book updated successfully',
            'data' => BookResource::make($updatedBook),
        ]);
    }

    #[Endpoint(
        title: 'Delete a book',
        description: 'Permanently remove a book from the library. This operation uses soft delete, so the book can be restored if needed. Any associated cover image will also be deleted.'
    )]
    public function destroy(DestroyBookRequest $request, Book $book): JsonResponse
    {
        $this->bookService->deleteBook($book);

        return $this->responseFactory->json([
            'message' => 'Book deleted successfully',
        ], 204);
    }
}
