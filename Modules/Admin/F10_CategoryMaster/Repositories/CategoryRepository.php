<?php

namespace Modules\Admin\F10_CategoryMaster\Repositories;

use App\Models\Content\Category;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class CategoryRepository
{
    protected Category $model;

    public function __construct(Category $model)
    {
        $this->model = $model;
    }

    public function all(): Collection
    {
        return $this->model->with(['parent', 'children'])
            ->orderBy('name')
            ->get();
    }

    public function getHierarchy()
    {
        return $this->model->whereNull('parent_id')
            ->with(['children' => fn($q) => $q->with('children')])
            ->orderBy('name')
            ->get();
    }

    public function find(string $id): ?Category
    {
        return $this->model->with(['parent', 'children'])->find($id);
    }

    public function create(array $data): Category
    {
        // Generate slug if not provided
        if (empty($data['slug']) && !empty($data['name'])) {
            $data['slug'] = \Str::slug($data['name']);
        }

        return $this->model->create($data);
    }

    public function update(string $id, array $data): bool
    {
        $category = $this->find($id);
        if (!$category) return false;

        // Generate slug if name changed
        if (isset($data['name']) && empty($data['slug'])) {
            $data['slug'] = \Str::slug($data['name']);
        }

        return $category->update($data);
    }

    public function delete(string $id): bool
    {
        $category = $this->find($id);
        if (!$category) return false;

        // Prevent delete if has children or posts
        if ($category->children()->exists() || $category->posts()->exists()) {
            throw new \Exception('Cannot delete category that has children or posts');
        }

        return $category->delete();
    }
}