<?php

namespace Modules\Admin\F8_CategoryTagManagement\Repositories;

use App\Models\Content\Tag;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class AdminTagRepository
{
    public function getAllPaginated(int $perPage = 15): LengthAwarePaginator
    {
        return Tag::orderBy('usage_count', 'desc')->paginate($perPage);
    }

    public function findById(string $id): ?Tag
    {
        return Tag::find($id);
    }

    public function create(array $data): Tag
    {
        $data['created_at'] = now();
        return Tag::create($data);
    }

    public function update(Tag $tag, array $data): bool
    {
        return $tag->update($data);
    }

    public function delete(Tag $tag): bool
    {
        return $tag->delete();
    }
}