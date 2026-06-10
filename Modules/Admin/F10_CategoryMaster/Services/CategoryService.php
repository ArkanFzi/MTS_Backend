<?php

namespace Modules\Admin\F10_CategoryMaster\Services;

use Modules\Admin\F10_CategoryMaster\Repositories\CategoryRepository;
use App\Models\Content\Category;
use Illuminate\Support\Facades\Cache;

class CategoryService
{
    public function __construct(
        protected CategoryRepository $repository
    ) {}

    public function getHierarchy()
    {
        return Cache::remember('all_categories', 3600, function () {
            return $this->repository->getHierarchy();
        });
    }

    public function find(string $id): ?Category
    {
        return $this->repository->find($id);
    }

    public function create(array $data): Category
    {
        $category = $this->repository->create($data);
        Cache::forget('all_categories');

        return $category;
    }

    public function update(string $id, array $data): bool
    {
        $result = $this->repository->update($id, $data);
        Cache::forget('all_categories');

        return $result;
    }

    public function delete(string $id): bool
    {
        $result = $this->repository->delete($id);
        Cache::forget('all_categories');

        return $result;
    }
}