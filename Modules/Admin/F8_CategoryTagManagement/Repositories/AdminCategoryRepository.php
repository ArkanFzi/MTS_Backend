<?php

namespace Modules\Admin\F8_CategoryTagManagement\Repositories;

use App\Models\Content\Category;
use Illuminate\Database\Eloquent\Collection;

class AdminCategoryRepository
{
    public function getAll(): Collection
    {
        return Category::with('parent')->orderBy('name', 'asc')->get();
    }

    public function findById(string $id): ?Category
    {
        return Category::find($id);
    }

    public function create(array $data): Category
    {
        $data['created_at'] = now();
        return Category::create($data);
    }

    public function update(Category $category, array $data): bool
    {
        return $category->update($data);
    }

    public function delete(Category $category): bool
    {
        return $category->delete();
    }
}