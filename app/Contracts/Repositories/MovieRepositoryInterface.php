<?php

namespace App\Contracts\Repositories;

interface MovieRepositoryInterface extends BaseRepositoryInterface
{
    public function filterByGenre(string $genre);

    public function filterByDirector(string $director);

    public function filterByStatus(string $status);

    public function filterByRating(float $minRating);
}
