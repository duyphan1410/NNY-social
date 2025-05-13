<?php

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use App\Http\Controllers\ImageController;
use App\Http\Requests\ProfileUpdateRequest;
use App\Models\Post;
use App\Models\PostImage;
use App\Models\PostVideo;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Log;
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
    public function photos(User $user): View
    {
        $photos = PostImage::whereHas('post', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })->get()->map(function ($photos) {
            return [
                'type' => 'photo',
                'url' => $photos->image_url,
                'created_at' => $photos->post->created_at,
            ];
        });

        return view('profile.photos', compact('user', 'photos'));
    }
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

        // Mặc định: album tổng hợp
        return view('profile.album', compact('user', 'mergedMedia'));

    }

    /**
     * Trang xem video hồ sơ
     */
    public function videos(User $user): View
    {
        $videos = PostVideo::whereHas('post', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })->get()->map(function ($video) {
            return [
                'type' => 'video',
                'url' => $video->video_url,
                'created_at' => $video->post->created_at,
            ];
        });

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
            $request->only([
                'bio',
                'location',
                'birthday',
                'gender',
                'website',
                'relationship_status',
                'hobbies',
                'social_links',
            ])
        );

        return Redirect::route('profile.me')->with('status', 'profile-updated');
    }

    public function updateAvatar(Request $request)
    {
        Log::info('Testing log in updateAvatar');

        $request->validate([
            'avatar' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $user = Auth::user();

        if ($request->hasFile('avatar')) {
            $imageController = new ImageController();
            Log::info('ImageController initialized');
            $imageUrls = $imageController->uploadAvatar($request->file('avatar')); // Gọi uploadAvatar

            Log::info('ImageUrls after upload:', ['urls' => $imageUrls]);

            if (empty($imageUrls) || !is_array($imageUrls) || empty($imageUrls[0])) {
                return redirect()->back()->with('error', 'Lỗi: Không upload được ảnh avatar lên Cloudinary.');
            }

            $avatarUrl = $imageUrls[0]; // Lấy URL đầu tiên (và duy nhất) của avatar

            // Tạo bài đăng mới
            $post = new Post();
            $post->user_id = auth()->id();
            $post->content = $user->first_name . ' ' . $user->last_name . ' đã đổi ảnh đại diện.';
            $post->save();

            // Lưu URL avatar vào post_images
            PostImage::create([
                'post_id' => $post->id,
                'image_url' => $avatarUrl,
            ]);

            // Cập nhật avatar của người dùng
            $user->avatar = $avatarUrl;
            $user->save();

            return Redirect::back()->with('success', 'Avatar đã được cập nhật và bài đăng đã được tạo thành công.');
        }

        return Redirect::back()->with('error', 'Có lỗi xảy ra khi tải lên avatar.');
    }

    public function updateCover(Request $request)
    {
        $request->validate([
            'cover_photo' => 'required|image|mimes:jpeg,png,jpg,gif|max:8192',
        ]);

        $user = Auth::user();

        if ($request->hasFile('cover_photo')) {
            $imageController = new ImageController();
            $coverUrl = $imageController->uploadCover($request->file('cover_photo'));

            if ($coverUrl) {
                // Cập nhật thông tin chi tiết người dùng
                if ($user->detail) {
                    $user->detail->cover_img_url = $coverUrl;
                    $user->detail->save();

                    // Tạo bài đăng thông báo sau khi cập nhật thành công
                    $post = new Post();
                    $post->user_id = auth()->id();
                    $post->content = $user->first_name . ' ' . $user->last_name . ' đã đổi ảnh bìa.';
                    $post->save();

                    // Lưu URL ảnh bìa vào PostImage (tùy chọn)
                    PostImage::create([
                        'post_id' => $post->id,
                        'image_url' => $coverUrl,
                    ]);

                    return Redirect::back()->with('success', 'Ảnh bìa đã được cập nhật thành công.');
                } else {
                    return Redirect::back()->with('error', 'Lỗi: Không tìm thấy thông tin chi tiết người dùng.');
                }
            } else {
                return Redirect::back()->with('error', 'Lỗi: Không upload được ảnh bìa.');
            }
        }

        return Redirect::back()->with('error', 'Vui lòng chọn một ảnh bìa để tải lên.');
    }
}
