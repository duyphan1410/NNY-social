<?php

namespace App\Http\Controllers\Home;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Collection;
use App\Models\Post;
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
            $userId = Auth::id();

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
            $posts = Post::withEngagement()
                ->whereIn('user_id', $friendIds)
                ->withExists(['likes as user_has_liked' => function ($query) {
                    $query->where('user_id', auth()->id());
                }])
                ->get()
                ->sortByDesc(function ($post) use ($userId, $friendIds) {
                    $priorityScore = 0;
                    $baseTimeScore = $post->created_at->timestamp; // Sử dụng timestamp làm cơ sở cho độ cũ
                    $friendIdsArray = $friendIds->toArray(); // Chuyển đổi Collection thành mảng

                    // Ưu tiên cao nhất cho bài đăng của bản thân và chưa tương tác
                    if ($post->user_id === $userId && !$post->user_has_liked) {
                        $priorityScore += 1000 + $baseTimeScore; // Thêm thời gian để duy trì thứ tự tương đối
                    }
                    // Ưu tiên cho bài đăng của bạn bè mà chưa thích
                    elseif (in_array($post->user_id, $friendIdsArray) && !$post->user_has_liked) {
                        $priorityScore += 800 + $baseTimeScore;
                    }
                    // Ưu tiên cho bài đăng có nhiều like và comment
                    else {
                        $engagementScore = ($post->likes_count * 3) + ($post->comments_count * 5); // Tăng trọng số tương tác
                        $priorityScore += $engagementScore * 10 + $baseTimeScore;
                    }

                    // Cộng thêm điểm dựa trên độ cũ (số giây đã qua - bài cũ hơn có điểm cao hơn)
                    $ageInSeconds = now()->diffInSeconds($post->created_at);
                    $priorityScore += $ageInSeconds * 0.1; // Hệ số nhỏ để tránh lấn át các yếu tố khác

                    return $priorityScore;
                })
                ->take(50);


            if (Auth::check()) {

                foreach ($posts as $post) {
                    $post->user_has_liked = $post->isLikedByUser($userId);
                }
            }


            // Lấy danh sách bạn bè (đơn giản hóa)
            $friends = Friend::where('user_id', Auth::id())
                ->orWhere('friend_id', Auth::id())
                ->with(['user', 'friend'])
                ->limit(20)
                ->get()
                ->map(function ($friend) {
                    return $friend->user_id === Auth::id() ? $friend->friend : $friend->user;
                });

            return view('home.home', compact('posts', 'friends'));

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Đã xảy ra lỗi: ' . $e->getMessage());
        }
    }
}
