<?php

namespace Modules\Admin\F10_CategoryMaster\Services;

use Modules\Admin\F10_CategoryMaster\Repositories\CategoryRepository;
use App\Models\Content\Category;

class CategoryService
{
    public function __construct(
        protected CategoryRepository $repository
    ) {}

    public function getHierarchy()
    {
        return $this->repository->getHierarchy();
    }

    public function find(string $id): ?Category
    {
        return $this->repository->find($id);
    }

    public function create(array $data): Category
    {
        // Kamu bisa tambahkan logika bisnis tambahan di sini jika perlu,
        // misalnya trigger event atau logging.
        return $this->repository->create($data);
    }

    public function update(string $id, array $data): bool
    {
        return $this->repository->update($id, $data);
    }

    public function delete(string $id): bool
    {
        return $this->repository->delete($id);
    }
}