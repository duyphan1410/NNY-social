<?php

namespace App\Http\Controllers\Post;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Comment;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    public function store(Request $request, $postId)
    {
        $request->validate([
            'content' => 'required|string|max:500',
        ]);

        Comment::create([
            'user_id' => Auth::id(),
            'post_id' => $postId,
            'content' => $request->input('content'),
        ]);

        return back()->with('success', 'Bình luận đã được thêm.');
    }

    public function destroy($id)
    {
        $comment = Comment::findOrFail($id);

        // Chỉ cho xoá nếu là người tạo bình luận hoặc admin
        if ($comment->user_id !== Auth::id()) {
            return back()->with('error', 'Bạn không có quyền xoá bình luận này.');
        }

        $comment->delete();

        return back()->with('success', 'Bình luận đã được xoá.');
    }
}
