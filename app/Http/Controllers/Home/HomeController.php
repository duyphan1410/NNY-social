<?php

namespace App\Http\Controllers\Home;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Post;
use App\Models\Reel;
use App\Models\Friend;

class HomeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        try {
            // Lấy danh sách bạn bè
            $friendIds = Friend::where(function($query) {
                $query->where('user_id', Auth::id())
                    ->orWhere('friend_id', Auth::id());
            })
                ->get()
                ->map(function ($friend) {
                    return $friend->user_id === Auth::id()
                        ? $friend->friend_id
                        : $friend->user_id;
                })
                ->unique()
                ->push(Auth::id());

            // Lấy bài viết
            $posts = Post::whereIn('user_id', $friendIds)
                ->with(['user', 'images', 'videos', 'comments.user'])
                ->withCount(['likes', 'comments'])
                ->withExists(['likes as user_has_liked' => function ($query) {
                    $query->where('user_id', auth()->id());
                }])
                ->get()
                ->sortByDesc(function ($post) {
                    // Công thức tính điểm ưu tiên
                    $engagementScore = ($post->likes_count * 1) + ($post->comments_count * 2);
                    $freshnessScore = 1 / (1 + now()->diffInHours($post->created_at));

                    // Người dùng thân thiết (ví dụ: tương tác nhiều) có thể nhân hệ số
                    $relationshipWeight = 1;

                    return ($engagementScore + $freshnessScore) * $relationshipWeight;
                })
                ->take(50);

            // Lấy reels (giữ nguyên)
            $reels = Reel::with('user')
                ->whereIn('user_id', $friendIds) // Chỉ hiện reels của bạn bè
                ->latest()
                ->limit(10) // Giới hạn số lượng
                ->get();

            // Lấy danh sách bạn bè (đơn giản hóa)
            $friends = Friend::where('user_id', Auth::id())
                ->orWhere('friend_id', Auth::id())
                ->with(['user', 'friend'])
                ->limit(20)
                ->get()
                ->map(function ($friend) {
                    return $friend->user_id === Auth::id() ? $friend->friend : $friend->user;
                });

            return view('home.home', compact('posts', 'reels', 'friends'));

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Đã xảy ra lỗi: ' . $e->getMessage());
        }
    }
}
