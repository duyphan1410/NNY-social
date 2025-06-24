<?php

namespace App\Http\Controllers\Post;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Comment;
use App\Events\NewNotificationEvent;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

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
            'parent_comment_id' => $request->input('parent_comment_id'),

        ]);


        $postOwner = Post::findOrFail($postId)->user;
        $commenter = Auth::user();
        $commenterFullName = trim($commenter->first_name . ' ' . $commenter->last_name);
        $postDetailUrl = route('post.show', ['id' => $postId]) . '#comments-' . $comment->id;
        $parentId = $request->input('parent_comment_id');
        $content = $request->input('content');
        $mentionMatches = []; // kết quả của preg_match_all

        // ===== ✅ PHÂN TÍCH CÁC MENTION =====
        preg_match_all('/@\[(.*?)\]\(user:(\d+)\)/', $content, $mentionMatches, PREG_SET_ORDER);
        $notifiedUserIds = []; // Track tất cả user đã được thông báo

        // ===== ✅ XỬ LÝ REPLY TRƯỚC =====
        if ($parentId) {
            $parentComment = Comment::find($parentId);
            if ($parentComment && $parentComment->user_id !== $commenter->id) {
                $repliedUser = $parentComment->user;

                $message = "{$commenterFullName} đã trả lời bình luận của bạn.";
                event(new NewNotificationEvent($repliedUser->id, [
                    'message' => $message,
                    'url' => $postDetailUrl,
                    'type' => 'reply_comment',
                ]));

                $notifiedUserIds[] = $repliedUser->id;
            }
        }


        // ===== ✅ XỬ LÝ CÁC MENTION =====
        foreach ($mentionMatches as $match) {
            $mentionedUserId = (int) $match[2];

            if ($mentionedUserId !== $commenter->id && !in_array($mentionedUserId, $notifiedUserIds)) {
                $mentionedUser = User::find($mentionedUserId);
                if ($mentionedUser) {
                    $message = "{$commenterFullName} đã nhắc đến bạn trong một bình luận.";
                    event(new NewNotificationEvent($mentionedUserId, [
                        'message' => $message,
                        'url' => $postDetailUrl,
                        'type' => 'mention_comment',
                    ]));

                    $notifiedUserIds[] = $mentionedUserId;
                }
            }
        }


        // ===== ✅ THÔNG BÁO CHO CHỦ BÀI VIẾT =====
        if ($postOwner->id !== $commenter->id && !in_array($postOwner->id, $notifiedUserIds)) {
            $message = "{$commenterFullName} đã bình luận bài viết của bạn.";
            event(new NewNotificationEvent($postOwner->id, [
                'message' => $message,
                'url' => $postDetailUrl,
                'type' => 'new_comment',
            ]));
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
