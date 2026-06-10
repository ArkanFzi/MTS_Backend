<?php

namespace Modules\Auth\F1_Register\Services;

use Modules\Auth\F1_Register\Repositories\RegisterRepository;
use Modules\User\F26_NotificationSystem\Services\NotificationService;
use Illuminate\Validation\ValidationException;
use App\Models\Auth\User;

class RegisterService
{
    protected RegisterRepository $registerRepository;
    protected NotificationService $notificationService;

    public function __construct(RegisterRepository $registerRepository, NotificationService $notificationService)
    {
        $this->registerRepository = $registerRepository;
        $this->notificationService = $notificationService;
    }

    public function execute(array $data): User
    {
        if (!\App\Models\Auth\Role::where('name', 'user')->exists()) {
            throw ValidationException::withMessages([
                'error' => ['Role "user" belum dibuat. Jalankan Role Seeder terlebih dahulu.']
            ]);
        }

        $user = $this->registerRepository->create($data);

        $this->notificationService->createNotification(
            $user->id,
            $user->id,
            'complete_profile_reminder',
            $user->id,
            \App\Models\Auth\User::class
        );

        return $user; // return User object, bukan array
    }
}