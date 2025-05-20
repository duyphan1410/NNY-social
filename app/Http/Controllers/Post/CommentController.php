<?php

namespace App\Http\Controllers\Post;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Comment;
use App\Events\NewNotificationEvent;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    public function store(Request $request, $postId)
    {
        $request->validate([
            'content' => 'required|string|max:500',
        ]);

        $comment = Comment::create([
            'user_id' => Auth::id(),
            'post_id' => $postId,
            'content' => $request->input('content'),
        ]);

        // Lấy người tạo bài viết
        $postOwner = Post::findOrFail($postId)->user;
        $commenter = Auth::user();
        $commenterFullName = trim($commenter->first_name . ' ' . $commenter->last_name);
        $postDetailUrl = route('post.show', ['id' => $postId]) . '#comments-' . $postId; // Link đến bình luận

        // Kiểm tra xem bình luận có phải là trả lời hay không (dựa trên ký tự '@' ở đầu)
        if (preg_match('/@\[.*?\]\(user:(\d+)\)/', $request->input('content'), $matches)) {
            $mentionedUserId = $matches[1];

            if ($mentionedUserId != $commenter->id) {
                $repliedToUser = User::find($mentionedUserId);

                if ($repliedToUser) {
                    $message = $commenterFullName . ' đã trả lời bình luận của bạn trên bài viết.';
                    event(new NewNotificationEvent($repliedToUser->id, [
                        'message' => $message,
                        'url' => $postDetailUrl,
                        'type' => 'reply_comment',
                    ]));
                }
            }
        } else {
            // Gửi thông báo đến chủ bài viết nếu không phải là trả lời và người bình luận không phải là chủ bài viết
            if ($postOwner->id !== $commenter->id) {
                $message = $commenterFullName . ' đã bình luận bài viết của bạn.';
                event(new NewNotificationEvent($postOwner->id, [
                    'message' => $message,
                    'url' => $postDetailUrl,
                    'type' => 'new_comment',
                ]));
            }
        }

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
