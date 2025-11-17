<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Contracts\Repositories\BookRepositoryInterface;
use App\Models\Book;
use Illuminate\Database\Eloquent\Collection;

class BookRepository extends BaseRepository implements BookRepositoryInterface
{
    public function __construct(Book $model)
    {
        parent::__construct($model);
    }

    public function findByIsbn(string $isbn)
    {
        return $this->model->where('isbn', $isbn)->first();
    }

    public function filterByGenre(string $genre): Collection
    {
        return $this->model->where('genre', $genre)->get();
    }

    public function filterByAuthor(string $author): Collection
    {
        return $this->model->where('author', 'like', "%{$author}%")->get();
    }

    public function filterByStatus(string $status): Collection
    {
        return $this->model->where('status', $status)->get();
    }
}
