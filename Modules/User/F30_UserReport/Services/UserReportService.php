<?php

namespace Modules\User\F30_UserReport\Services;

use Modules\User\F30_UserReport\Repositories\UserReportRepository;
use Modules\User\F26_NotificationSystem\Services\NotificationService; // Import service notif
use Illuminate\Support\Facades\Auth;
use App\Models\Auth\User;
use App\Models\Moderation\Report;

class UserReportService
{
    protected $repository;
    protected $notificationService; // Tambahkan properti ini

    public function __construct(
        UserReportRepository $repository,
        NotificationService $notificationService // Inject via constructor
    ) {
        $this->repository = $repository;
        $this->notificationService = $notificationService;
    }

public function handleReport(array $data): Report
{
    $data['reporter_id'] = Auth::id();
    $data['status'] = 'pending';
    $data['created_at'] = now();

    $report = $this->repository->create($data);

    // LOGIKA PERBAIKAN:
    // Gunakan whereHas untuk mencari user yang memiliki role 'admin' atau 'moderator'
    $recipients = User::whereHas('roles', function ($query) {
        $query->whereIn('name', ['admin', 'moderator']);
    })->get();

    foreach ($recipients as $user) {
        $this->notificationService->createNotification(
            userId: $user->id,
            actorId: Auth::id(),
            type: 'new_report',
            refId: $report->id,
            refType: 'report' // Gunakan alias 'report' sesuai morphMap Anda
        );
    }

    return $report;
}
}