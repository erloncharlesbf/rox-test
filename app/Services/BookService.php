<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\Repositories\BookRepositoryInterface;
use App\Models\Book;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class BookService
{
    public function __construct(
        private readonly BookRepositoryInterface $bookRepository
    ) {}

    public function getAllBooks(): Collection
    {
        return $this->bookRepository->all();
    }

    public function paginateBooks(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        return $this->bookRepository->paginate($perPage, $filters);
    }

    public function findBook(int $id): ?Book
    {
        return $this->bookRepository->find($id);
    }

    public function createBook(array $data): Book
    {
        return DB::transaction(function () use ($data) {
            $coverFile = $data['cover'] ?? null;
            unset($data['cover']);

            $book = $this->bookRepository->create($data);

            if ($coverFile) {
                $this->attachCover($book, $coverFile);
            }

            return $book->load('cover');
        });
    }

    public function updateBook(Book $book, array $data): Book
    {
        return DB::transaction(function () use ($book, $data) {
            $coverFile = $data['cover'] ?? null;
            unset($data['cover']);

            $book->update($data);

            if ($coverFile) {
                if ($book->cover) {
                    Storage::disk('public')->delete($book->cover->file_path);
                    $book->cover->delete();
                }

                $this->attachCover($book, $coverFile);
            }

            return $book->load('cover');
        });
    }

    public function deleteBook(Book $book): bool
    {
        return DB::transaction(function () use ($book) {
            if ($book->cover) {
                Storage::disk('public')->delete($book->cover->file_path);
            }

            return $book->delete();
        });
    }

    public function findByIsbn(string $isbn): ?Book
    {
        return $this->bookRepository->findByIsbn($isbn);
    }

    private function attachCover(Book $book, $file): void
    {
        $path = $file->store('covers/books', 'public');

        $book->attachments()->create([
            'file_name' => $file->getClientOriginalName(),
            'file_path' => $path,
            'mime_type' => $file->getMimeType(),
            'file_size' => $file->getSize(),
            'type' => 'cover',
        ]);
    }
}
