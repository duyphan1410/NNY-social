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
            // ... xóa notification ...
            \Log::info('Đã xóa like.');
            $liked = false;
        } else {
            \Log::info('Chưa tìm thấy like, tiến hành tạo.');
            Like::create([
                'user_id' => $user->id,
                'post_id' => $postId,
            ]);
            \Log::info('Đã tạo like.');

            // Kiểm tra và tạo thông báo
            $existingNotification = Notification::where('user_id', $user->id)
                ->where('type', 'like_post')->first();

            if (!$existingNotification) {
                \Log::info('Chưa có thông báo, tiến hành tạo thông báo.');
                $postOwner = Post::findOrFail($postId)->user;;
                $fullName = trim($user->first_name . ' ' . $user->last_name);
                $message = $fullName . ' đã thích bài viết của bạn.';
                $postUrl = '/social-network/public/post/' . $postId . '/detail';

                if ($postOwner->id !== $user->id) {
                    \Log::info('Người thích không phải chủ bài viết, dispatching event.');
                    try {
                        event(new NewNotificationEvent($postOwner->id, [
                            'message' => $message,
                            'url' => $postUrl,
                            'type' => 'like_post',
                        ]));
                        \Log::info('Đã dispatch event thành công.');
                    } catch (\Exception $e) {
                        \Log::error('Lỗi khi dispatch event: ' . $e->getMessage());
                    }
                } else {
                    \Log::info('Người thích là chủ bài viết, không tạo thông báo.');
                }
            } else {
                \Log::info('Đã có thông báo trước đó.');
            }
            $liked = true;
        }

        \Log::info('Kết thúc toggleLike, trả về: ' . json_encode(['liked' => $liked]));
        return response()->json(['liked' => $liked]);
    }
}
