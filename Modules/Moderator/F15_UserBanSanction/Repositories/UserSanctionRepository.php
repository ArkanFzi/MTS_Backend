<?php

namespace Modules\Admin\F15_UserBanSanction\Repositories;

use App\Models\Auth\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class UserSanctionRepository
{
    protected User $model;

    public function __construct(User $model)
    {
        $this->model = $model;
    }

    public function getAllPaginated(int $perPage = 15, $search = null): LengthAwarePaginator
    {
        return $this->model
            ->when($search, function ($query, $search) {
                $query->where('username', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
            })
            ->orderBy('is_banned', 'desc')
            ->orderBy('username')
            ->paginate($perPage);
    }

    public function findById(string $id): ?User
    {
        return $this->model->find($id);
    }

    public function updateBanStatus(string $id, bool $isBanned): bool
    {
        return $this->model->where('id', $id)->update([
            'is_banned' => $isBanned,
        ]);
    }
}