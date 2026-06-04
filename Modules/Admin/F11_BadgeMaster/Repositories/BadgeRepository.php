<?php

namespace Modules\Admin\F11_BadgeMaster\Repositories;

use App\Models\Gamification\Badge;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class BadgeRepository
{
    protected Badge $model;

    public function __construct(Badge $model)
    {
        $this->model = $model;
    }

    public function getAllPaginated(int $perPage = 15, $search = null): LengthAwarePaginator
    {
        return $this->model
            ->when($search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%");
            })
            ->orderBy('tier')
            ->orderBy('condition_value')
            ->paginate($perPage);
    }

    public function findById(string $id): ?Badge
    {
        return $this->model->find($id);
    }

    public function create(array $data): Badge
    {
        return $this->model->create($data);
    }

    public function update(string $id, array $data): bool
    {
        return $this->model->where('id', $id)->update($data);
    }

    public function delete(string $id): bool
    {
        return $this->model->where('id', $id)->delete();
    }

    public function getAllForSelect(): Collection
    {
        return $this->model->orderBy('tier')->orderBy('name')->get();
    }
}