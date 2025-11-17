<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Contracts\Repositories\MovieRepositoryInterface;
use App\Models\Movie;
use Illuminate\Database\Eloquent\Collection;

class MovieRepository extends BaseRepository implements MovieRepositoryInterface
{
    public function __construct(Movie $model)
    {
        parent::__construct($model);
    }

    public function filterByGenre(string $genre): Collection
    {
        return $this->model->where('genre', $genre)->get();
    }

    public function filterByDirector(string $director): Collection
    {
        return $this->model->where('director', 'like', "%{$director}%")->get();
    }

    public function filterByStatus(string $status): Collection
    {
        return $this->model->where('status', $status)->get();
    }

    public function filterByRating(float $minRating): Collection
    {
        return $this->model->where('rating', '>=', $minRating)->get();
    }
}
