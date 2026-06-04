<?php

namespace Modules\Admin\F8_CategoryTagManagement\Services;

use App\Models\Content\Category;
use App\Models\Content\Tag;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Modules\Admin\F8_CategoryTagManagement\Repositories\AdminCategoryRepository;
use Modules\Admin\F8_CategoryTagManagement\Repositories\AdminTagRepository;

class CategoryTagService
{
    protected AdminCategoryRepository $categoryRepo;
    protected AdminTagRepository $tagRepo;

    public function __construct(AdminCategoryRepository $categoryRepo, AdminTagRepository $tagRepo)
    {
        $this->categoryRepo = $categoryRepo;
        $this->tagRepo = $tagRepo;
    }

    // --- LOGIC KATEGORI ---
    public function getCategories(): Collection
    {
        return $this->categoryRepo->getAll();
    }

    public function createCategory(array $data): Category
    {
        return $this->categoryRepo->create($data);
    }

    public function deleteCategory(string $id): void
    {
        $category = $this->categoryRepo->findById($id);
        if (!$category) {
            throw new \Exception("Kategori tidak ditemukan.", 404);
        }
        $this->categoryRepo->delete($category);
    }

    // --- LOGIC TAG ---
    public function getTags(int $perPage): LengthAwarePaginator
    {
        return $this->tagRepo->getAllPaginated($perPage);
    }

    public function createTag(array $data): Tag
    {
        return $this->tagRepo->create($data);
    }

    public function deleteTag(string $id): void
    {
        $tag = $this->tagRepo->findById($id);
        if (!$tag) {
            throw new \Exception("Tag tidak ditemukan.", 404);
        }
        $this->tagRepo->delete($tag);
    }
}