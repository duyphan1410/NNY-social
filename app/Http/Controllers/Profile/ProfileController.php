<?php

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProfileUpdateRequest;
use App\Models\Post;
use App\Models\PostImage;
use App\Models\PostVideo;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Xem hồ sơ của chính mình
     */
    public function myProfile(Request $request): View
    {
        $user = $request->user()->load('detail');
        $posts = Post::where('user_id', Auth::id())->orderBy('created_at', 'desc')->paginate(10);
        return view('profile.my', compact('user', 'posts')); // Hoặc 'profile.show' nếu bạn muốn dùng chung view
    }

    /**
     * Xem hồ sơ của người khác (bằng id) - Trang chính (Bài viết)
     */
    public function show(User $user): View
    {
        $posts = Post::where('user_id', $user->id)->orderBy('created_at', 'desc')->paginate(10);
        return view('profile.show', compact('user', 'posts'));
    }

    /**
     * Trang giới thiệu hồ sơ
     */
    public function about(User $user): View
    {
        return view('profile.about', compact('user'));
    }

    /**
     * Trang xem ảnh hồ sơ
     */
    public function album(User $user): View
    {
        $photos = PostImage::whereHas('post', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })->get()->map(function ($photo) {
            return [
                'type' => 'photo',
                'url' => $photo->image_url,
                'created_at' => $photo->post->created_at,
            ];
        });

        $videos = PostVideo::whereHas('post', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })->get()->map(function ($video) {
            return [
                'type' => 'video',
                'url' => $video->video_url,
                'created_at' => $video->post->created_at,
            ];
        });

        // Trộn và sắp xếp theo created_at (từ sớm đến lâu nhất - asc())
        $mergedMedia = $photos->concat($videos)->sortByDesc('created_at');

        return view('profile.album', compact('user', 'mergedMedia'));
    }

    /**
     * Trang xem video hồ sơ
     */
    public function videos(User $user): View
    {
        $videos = PostVideo::whereHas('post', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })->latest()->paginate(12);

        return view('profile.videos', compact('user', 'videos'));
    }

    /**
     * Trang chỉnh sửa hồ sơ
     */
    public function edit(Request $request): View
    {
        $user = $request->user()->load('detail');
        return view('profile.edit', compact('user'));
    }

    /**
     * Cập nhật hồ sơ cá nhân
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();

        // Cập nhật các trường cơ bản của user
        $user->fill($request->only(['first_name', 'last_name']));
        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }
        $user->save();

        // Cập nhật user_detail
        $user->detail()->updateOrCreate(
            ['user_id' => $user->id],
            $request->only(['bio', 'location', 'birthday', 'gender', 'cover']) // tùy form
        );

        return Redirect::route('profile.me')->with('status', 'profile-updated');
    }
}
