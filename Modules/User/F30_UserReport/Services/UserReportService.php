<?php

namespace Modules\User\F30_UserReport\Services;

use Modules\User\F30_UserReport\Repositories\UserReportRepository;
use Modules\User\F26_NotificationSystem\Services\NotificationService;
use Illuminate\Support\Facades\Auth;
use App\Models\Auth\User;
use App\Models\Moderation\Report;

class UserReportService
{
    protected $repository;
    protected $notificationService;

    public function __construct(
        UserReportRepository $repository,
        NotificationService $notificationService
    ) {
        $this->repository = $repository;
        $this->notificationService = $notificationService;
    }

    public function handleReport(array $data): Report
    {
        $data['reporter_id'] = Auth::id();

        // Cek duplikat
        $existing = $this->repository->findExisting(
            Auth::id(),
            $data['target_id'],
            $data['target_type']
        );

        if ($existing) {
            abort(409, 'Anda sudah pernah melaporkan konten ini.');
        }

        $data['status'] = 'pending';
        $data['created_at'] = now();

        $report = $this->repository->create($data);

        $recipients = User::whereHas('roles', function ($query) {
            $query->whereIn('name', ['admin', 'moderator']);
        })->get();

        foreach ($recipients as $user) {
            $this->notificationService->createNotification(
                userId: $user->id,
                actorId: Auth::id(),
                type: 'new_report',
                refId: $report->id,
                refType: 'report'
            );
        }

        return $report;
    }
}