<?php

namespace Modules\Admin\F11_BadgeMaster\Services;

use Modules\Admin\F11_BadgeMaster\Repositories\BadgeRepository;
use Illuminate\Support\Str;

class BadgeService
{
    protected BadgeRepository $repository;

    public function __construct(BadgeRepository $repository)
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
        $data['created_at'] = now();

        return $this->repository->create($data);
    }

    public function update(string $id, array $data)
    {
        return $this->repository->update($id, $data);
    }

    public function delete(string $id): bool
    {
        return $this->repository->delete($id);
    }
}