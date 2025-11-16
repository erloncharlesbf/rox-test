<?php

namespace App\Services;

use App\Contracts\Repositories\MovieRepositoryInterface;
use App\Models\Movie;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class MovieService
{
    public function __construct(
        private readonly MovieRepositoryInterface $movieRepository, private readonly \Illuminate\Database\DatabaseManager $databaseManager, private readonly \Illuminate\Filesystem\FilesystemManager $filesystemManager
    ) {}

    public function getAllMovies(): Collection
    {
        return $this->movieRepository->all();
    }

    public function paginateMovies(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        return $this->movieRepository->paginate($perPage, $filters);
    }

    public function findMovie(int $id): ?Movie
    {
        return $this->movieRepository->find($id);
    }

    public function createMovie(array $data): Movie
    {
        return $this->databaseManager->transaction(function () use ($data) {
            $coverFile = $data['cover'] ?? null;
            unset($data['cover']);

            $movie = $this->movieRepository->create($data);

            if ($coverFile) {
                $this->attachCover($movie, $coverFile);
            }

            return $movie->load('cover');
        });
    }

    public function updateMovie(Movie $movie, array $data): Movie
    {
        return $this->databaseManager->transaction(function () use ($movie, $data) {
            $coverFile = $data['cover'] ?? null;
            unset($data['cover']);

            $movie->update($data);

            if ($coverFile) {
                if ($movie->cover) {
                    $this->filesystemManager->disk('public')->delete($movie->cover->file_path);
                    $movie->cover->delete();
                }

                $this->attachCover($movie, $coverFile);
            }

            return $movie->load('cover');
        });
    }

    public function deleteMovie(Movie $movie): bool
    {
        return $this->databaseManager->transaction(function () use ($movie) {
            if ($movie->cover) {
                $this->filesystemManager->disk('public')->delete($movie->cover->file_path);
            }

            return $movie->delete();
        });
    }

    private function attachCover(Movie $movie, $file): void
    {
        $path = $file->store('covers/movies', 'public');

        $movie->attachments()->create([
            'file_name' => $file->getClientOriginalName(),
            'file_path' => $path,
            'mime_type' => $file->getMimeType(),
            'file_size' => $file->getSize(),
            'type' => 'cover',
        ]);
    }
}
