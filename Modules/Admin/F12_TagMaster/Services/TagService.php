<?php

namespace Modules\Admin\F12_TagMaster\Services;

use Modules\Admin\F12_TagMaster\Repositories\TagRepository;
use Illuminate\Support\Str;

class TagService
{
    protected TagRepository $repository;

    public function __construct(TagRepository $repository)
    {
        $this->repository = $repository;
    }

    public function getAllPaginated(int $perPage = 15, $search = null)
    {
        return $this->repository->getAllPaginated($perPage, $search);
    }

    public function findById(string $id)
    {
        return $this->repository->findById($id);
    }

    public function create(array $data)
    {
        $data['slug'] = Str::slug($data['name']);
        $data['usage_count'] = 0;
        $data['created_at'] = now();

        return $this->repository->create($data);
    }

    public function update(string $id, array $data)
    {
        if (isset($data['name'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        return $this->repository->update($id, $data);
    }

    public function delete(string $id): bool
    {
        return $this->repository->delete($id);
    }
}