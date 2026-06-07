<?php

namespace Modules\Admin\F9_UserManagement\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use App\Models\Auth\User;
use App\Models\Content\Post;
use App\Models\Gamification\PointsLog;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AdminDashboardController extends Controller
{
    public function overview(): JsonResponse
    {
        // Ambil data untuk 7 hari terakhir
        $days = 7;
        $startDate = Carbon::now()->subDays($days - 1)->startOfDay();

        // Stats Registrasi User Harian
        $userRegistrations = User::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('count(*) as count')
        )
        ->where('created_at', '>=', $startDate)
        ->groupBy('date')
        ->get()
        ->pluck('count', 'date');

        // Stats Pembuatan Post Harian
        $postCreations = Post::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('count(*) as count')
        )
        ->where('created_at', '>=', $startDate)
        ->groupBy('date')
        ->get()
        ->pluck('count', 'date');

        // Format data untuk Chart (Labels & Datasets)
        $labels = [];
        $userData = [];
        $postData = [];

        for ($i = 0; $i < $days; $i++) {
            $date = Carbon::now()->subDays($days - 1 - $i)->format('Y-m-d');
            $labels[] = $date;
            $userData[] = $userRegistrations->get($date, 0);
            $postData[] = $postCreations->get($date, 0);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'labels' => $labels,
                'datasets' => [
                    [
                        'label' => 'User Registration',
                        'data' => $userData
                    ],
                    [
                        'label' => 'Post Creation',
                        'data' => $postData
                    ]
                ],
                'summary' => [
                    'total_users' => User::count(),
                    'total_posts' => Post::count(),
                    'active_users_today' => User::whereDate('created_at', Carbon::today())->count(),
                    'posts_today' => Post::whereDate('created_at', Carbon::today())->count(),
                ]
            ]
        ]);
    }

    public function pointsSummary(): JsonResponse
    {
        $stats = PointsLog::select(
            DB::raw('SUM(CASE WHEN points > 0 THEN points ELSE 0 END) as total_earned'),
            DB::raw('SUM(CASE WHEN points < 0 THEN points ELSE 0 END) as total_deducted'),
            DB::raw('SUM(points) as net_circulation')
        )->first();

        $breakdown = PointsLog::select(
            'action_type',
            DB::raw('SUM(points) as total')
        )
        ->groupBy('action_type')
        ->orderBy('total', 'desc')
        ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'total_earned' => (int) ($stats->total_earned ?? 0),
                'total_deducted' => (int) ($stats->total_deducted ?? 0),
                'net_circulation' => (int) ($stats->net_circulation ?? 0),
                'breakdown' => $breakdown
            ]
        ]);
    }
}
