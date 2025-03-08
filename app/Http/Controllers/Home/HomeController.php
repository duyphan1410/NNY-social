<?php

namespace App\Http\Controllers\Home;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\Reel;

class HomeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        try {
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

            return view('home.home', compact('posts', 'reels'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Đã xảy ra lỗi: ' . $e->getMessage());
        }
    }
}
