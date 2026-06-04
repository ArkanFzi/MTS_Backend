<?php

namespace Modules\Admin\F12_TagMaster\Repositories;

use App\Models\Content\Tag;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class TagRepository
{
    protected Tag $model;

    public function __construct(Tag $model)
    {
        $this->model = $model;
    }

    public function getAllPaginated(int $perPage = 15, $search = null): LengthAwarePaginator
    {
        return $this->model
            ->when($search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%")
                      ->orWhere('slug', 'like', "%{$search}%");
            })
            ->orderBy('usage_count', 'desc')
            ->orderBy('name')
            ->paginate($perPage);
    }

    public function findById(string $id): ?Tag
    {
        return $this->model->find($id);
    }

    public function create(array $data): Tag
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

    public function getForSelect(): Collection
    {
        return $this->model->orderBy('name')->get(['id', 'name', 'color']);
    }
}