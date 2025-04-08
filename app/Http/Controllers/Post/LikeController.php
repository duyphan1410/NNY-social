<?php

namespace App\Http\Controllers\Post;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Like;
use App\Models\Post;
use Illuminate\Support\Facades\Auth;

class LikeController extends Controller
{
    public function toggleLike(Request $request, $postId)
    {
        $user = Auth::user();
        $like = Like::where('user_id', $user->id)->where('post_id', $postId)->first();

        if ($like) {
            $like->delete();
            $liked = false;
        } else {
            Like::create([
                'user_id' => $user->id,
                'post_id' => $postId
            ]);
            $liked = true;
        }

        $likesCount = Like::where('post_id', $postId)->count();

        return response()->json([
            'liked' => $liked,
            'likes_count' => $likesCount
        ]);
    }
}
