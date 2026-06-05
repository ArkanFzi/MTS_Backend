<?php

namespace Modules\Moderator\F13_ContentReportQueue\Repositories;

use App\Models\Moderation\Report;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ReportRepository
{
    protected Report $model;

    public function __construct(Report $model)
    {
        $this->model = $model;
    }

    public function getAllPaginated(int $perPage = 20, $status = null, $search = null): LengthAwarePaginator
    {
        return $this->model
            ->with(['reporter', 'resolver', 'target'])
            ->when($status, fn($q) => $q->where('status', $status))
            ->when($search, function ($q, $search) {
                $q->whereHas('target', function ($tq) use ($search) {
                    $tq->where('title', 'like', "%{$search}%")
                       ->orWhere('body', 'like', "%{$search}%");
                })->orWhereHas('reporter', fn($rq) => $rq->where('username', 'like', "%{$search}%"));
            })
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    public function findById(string $id): ?Report
    {
        return $this->model->with(['reporter', 'target', 'resolver'])->find($id);
    }

    public function update(string $id, array $data): bool
    {
        return $this->model->where('id', $id)->update($data);
    }
}