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

            //Lấy ds bạn
            $friends = Friend::where('user_id', Auth::id())
                ->orWhere('friend_id', Auth::id())
                ->with(['user', 'friend'])
                ->get()
                ->map(function ($friend) {
                    return $friend->user_id === Auth::id() ? $friend->friend : $friend->user;
                });


            // Lấy danh sách reels mới nhất
            $reels = Reel::with('user')->latest()->get();

            // Lấy danh sách bài viết mới nhất
            $posts = Post::with(['user', 'images', 'videos', 'comments.user'])
                ->withCount(['likes', 'comments'])
                ->withExists(['likes as user_has_liked' => function ($query) {
                    $query->where('user_id', auth()->id());
                }])
                ->orderBy('created_at', 'desc')
                ->get();

            return view('home.home', compact('posts', 'reels','friends'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Đã xảy ra lỗi: ' . $e->getMessage());
        }
    }
}
