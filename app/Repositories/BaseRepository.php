<?php

namespace App\Repositories;

use App\Contracts\Repositories\BaseRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;

abstract class BaseRepository implements BaseRepositoryInterface
{
    public function __construct(protected Model $model)
    {
    }

    public function all(): Collection
    {
        return $this->model->all();
    }

    public function paginate(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        $query = $this->model->newQuery();

        foreach ($filters as $field => $value) {
            if ($value !== null && $value !== '') {
                if (is_array($value)) {
                    $query->whereIn($field, $value);
                } else {
                    $query->where($field, 'like', "%{$value}%");
                }
            }
        }

        return $query->paginate($perPage);
    }

    public function find(int $id): ?Model
    {
        return $this->model->find($id);
    }

    public function create(array $data): Model
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): bool
    {
        $model = $this->find($id);

        if (!$model instanceof \Illuminate\Database\Eloquent\Model) {
            return false;
        }

        return $model->update($data);
    }

    public function delete(int $id): bool
    {
        $model = $this->find($id);

        if (!$model instanceof \Illuminate\Database\Eloquent\Model) {
            return false;
        }

        return $model->delete();
    }

    public function findBy(string $field, mixed $value): ?Model
    {
        return $this->model->where($field, $value)->first();
    }

    public function findWhere(array $conditions): Collection
    {
        $query = $this->model->newQuery();

        foreach ($conditions as $field => $value) {
            $query->where($field, $value);
        }

        return $query->get();
    }
}
