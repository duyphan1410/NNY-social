<?php

namespace App\Http\Controllers\Post;

use App\Events\NewNotificationEvent;
use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;
use App\Models\Like;
use App\Models\Post;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class LikeController extends Controller
{
    public function toggleLike(Request $request, $postId)
    {
        \Log::info('Bắt đầu toggleLike cho post ID: ' . $postId . ', user ID: ' . Auth::id());

        $user = Auth::user();
        $like = Like::where('user_id', $user->id)->where('post_id', $postId)->first();

        if ($like) {
            \Log::info('Đã tìm thấy like, tiến hành xóa.');
            $like->delete();

            // Xóa notification like tương ứng
            $postOwner = Post::findOrFail($postId)->user;
            if ($postOwner->id !== $user->id) {
                Notification::where('user_id', $postOwner->id)
                    ->where('type', 'like_post')
                    ->where('data->post_id', $postId) // Nếu bạn lưu post_id trong data
                    ->delete();
            }

            \Log::info('Đã xóa like và notification.');
            $liked = false;
        } else {
            \Log::info('Chưa tìm thấy like, tiến hành tạo.');
            Like::create([
                'user_id' => $user->id,
                'post_id' => $postId,
            ]);
            \Log::info('Đã tạo like.');

            // Tạo thông báo cho chủ bài viết
            $postOwner = Post::findOrFail($postId)->user;

            if ($postOwner->id !== $user->id) {
                \Log::info('Người thích không phải chủ bài viết, tiến hành tạo thông báo.');

                $fullName = trim($user->first_name . ' ' . $user->last_name);
                $message = $fullName . ' đã thích bài viết của bạn.';
                $postUrl = '/social-network/public/post/' . $postId . '/detail';

                try {
                    event(new NewNotificationEvent($postOwner->id, [
                        'message' => $message,
                        'url' => $postUrl,
                        'type' => 'like_post',
                        'post_id' => $postId, // Thêm post_id để phân biệt
                    ]));
                    \Log::info('Đã dispatch event thành công.');
                } catch (\Exception $e) {
                    \Log::error('Lỗi khi dispatch event: ' . $e->getMessage());
                }
            } else {
                \Log::info('Người thích là chủ bài viết, không tạo thông báo.');
            }

            $liked = true;
        }

        // Đếm lại số like sau khi đã thêm hoặc xóa
        $likesCount = Post::find($postId)->likes()->count();

        \Log::info('Kết thúc toggleLike, trả về: ' . json_encode(['liked' => $liked, 'likes_count' => $likesCount]));
        return response()->json([
            'liked' => $liked,
            'likes_count' => $likesCount,
        ]);
    }
}
