<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Post;
use App\Models\Comment;
use App\Models\Like;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StatisticsController extends Controller
{
    public function index(Request $request)
    {
        $now = now();

        // 1. Tổng số người dùng mới theo tháng trong 6 tháng qua
        $newUsersMonthly = User::select(DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'), DB::raw('COUNT(*) as count'))
            ->where('created_at', '>=', now()->subMonths(6))
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // 2. Tổng số bài đăng theo tháng trong 6 tháng qua
        $postsMonthly = Post::select(DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'), DB::raw('COUNT(*) as count'))
            ->where('created_at', '>=', now()->subMonths(6))
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // 3. Tổng số người dùng bị khóa
        $bannedUsers = User::where('banned', true)->count();

        // 4. Tổng số like và comment trong 30 ngày qua
        $interactions = [
            'likes' => Like::where('created_at', '>=', now()->subDays(30))->count(),
            'comments' => Comment::where('created_at', '>=', now()->subDays(30))->count(),
        ];

        // 5. Top 5 người dùng có nhiều bài đăng nhất
        $topUsers = User::withCount('posts')
            ->orderByDesc('posts_count')
            ->take(5)
            ->get();

        return view('admin.statistics.index', compact(
            'newUsersMonthly',
            'postsMonthly',
            'bannedUsers',
            'interactions',
            'topUsers'
        ));
    }
}
