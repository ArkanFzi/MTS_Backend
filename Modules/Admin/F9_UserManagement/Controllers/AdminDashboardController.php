<?php

namespace Modules\Admin\F9_UserManagement\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use App\Models\Auth\User;
use App\Models\Content\Post;
use App\Models\Content\Comment;
use App\Models\Content\Category;
use App\Models\Content\Tag;
use App\Models\Gamification\Badge;
use App\Models\Gamification\PointsLog;
use App\Models\Moderation\ModerationLog;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AdminDashboardController extends Controller
{
    public function overview(): JsonResponse
    {
        return response()->json([
            'status'  => 'success',
            'message' => 'Dashboard statistics retrieved successfully.',
            'data'    => [
                'total_users'      => User::count(),
                'total_posts'      => Post::count(),
                'total_comments'   => Comment::count(),
                'total_categories' => Category::count(),
                'total_tags'       => Tag::count(),
                'total_badges'     => Badge::count(),
                'new_users_today'  => User::whereDate('created_at', Carbon::today())->count(),
                'new_posts_today'  => Post::whereDate('created_at', Carbon::today())->count(),
            ],
        ]);
    }

    public function pointsSummary(): JsonResponse
    {
        $topEarners = PointsLog::select(
            'user_id',
            DB::raw('SUM(points) as total_points'),
            DB::raw('COUNT(*) as actions_count')
        )
        ->where('points', '>', 0)
        ->groupBy('user_id')
        ->orderByDesc('total_points')
        ->limit(20)
        ->get()
        ->map(function ($entry) {
            $user = User::find($entry->user_id);
            return [
                'user_id'       => $entry->user_id,
                'username'      => $user?->username ?? 'Unknown',
                'total_points'  => (int) $entry->total_points,
                'actions_count' => (int) $entry->actions_count,
            ];
        });

        return response()->json([
            'status'  => 'success',
            'message' => 'Points summary retrieved successfully.',
            'data'    => $topEarners,
        ]);
    }

    public function activityChart(): JsonResponse
    {
        $days = 14;
        $startDate = Carbon::today()->subDays($days - 1);

        $posts = Post::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as count')
            )
            ->where('created_at', '>=', $startDate)
            ->groupBy('date')
            ->pluck('count', 'date');

        $comments = Comment::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as count')
            )
            ->where('created_at', '>=', $startDate)
            ->groupBy('date')
            ->pluck('count', 'date');

        $data = [];
        for ($d = 0; $d < $days; $d++) {
            $date = $startDate->copy()->addDays($d)->format('Y-m-d');
            $data[] = [
                'date'     => Carbon::parse($date)->format('d M'),
                'posts'    => (int) ($posts[$date] ?? 0),
                'comments' => (int) ($comments[$date] ?? 0),
            ];
        }

        return response()->json([
            'status'  => 'success',
            'message' => 'Activity chart data retrieved successfully.',
            'data'    => $data,
        ]);
    }

    public function auditTimeline(): JsonResponse
    {
        $logs = ModerationLog::with(['moderator', 'targetUser'])
            ->orderByDesc('created_at')
            ->paginate(25);

        return response()->json([
            'status'  => 'success',
            'message' => 'Audit timeline retrieved successfully.',
            'data'    => $logs->items(),
            'meta'    => [
                'current_page' => $logs->currentPage(),
                'last_page'    => $logs->lastPage(),
                'per_page'     => $logs->perPage(),
                'total'        => $logs->total(),
            ],
        ]);
    }
}
