<?php

namespace Modules\User\F29_BadgeAchievement\Controllers;

use App\Http\Controllers\Controller;
use Modules\User\F29_BadgeAchievement\Services\BadgeAchievementService;

class BadgeAchievementController extends Controller
{
    protected BadgeAchievementService $service;

    public function __construct(BadgeAchievementService $service)
    {
        $this->service = $service;
    }
    
    public function index()
    {
        $user = auth()->user();
        
        // Mengambil badge dengan data pivot earned_at
        $badges = $user->badges()->withPivot('earned_at')->get();
        
        return response()->json([
            'success' => true,
            'data' => $badges
        ]);
    }
}
