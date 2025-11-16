<?php

namespace App\Contracts\Repositories;

interface BookRepositoryInterface extends BaseRepositoryInterface
{
    public function findByIsbn(string $isbn);

    public function filterByGenre(string $genre);

    public function filterByAuthor(string $author);

    public function filterByStatus(string $status);
}
