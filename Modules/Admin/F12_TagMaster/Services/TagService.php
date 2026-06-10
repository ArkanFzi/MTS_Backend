<?php

namespace Modules\Admin\F12_TagMaster\Services;

use Modules\Admin\F12_TagMaster\Repositories\TagRepository;
use Illuminate\Support\Facades\Cache;
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
        $data['slug']       = Str::slug($data['name']);
        $data['usage_count'] = 0;
        $data['created_at'] = now();

        $tag = $this->repository->create($data);
        Cache::forget('all_tags');

        return $tag;
    }

    public function update(string $id, array $data)
    {
        if (isset($data['name'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        $result = $this->repository->update($id, $data);
        Cache::forget('all_tags');

        return $result;
    }

    public function delete(string $id): bool
    {
        $result = $this->repository->delete($id);
        Cache::forget('all_tags');

        return $result;
    }
}