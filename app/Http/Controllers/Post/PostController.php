<?php

namespace App\Http\Controllers\Post;

use App\Events\NewNotificationEvent;
use App\Http\Controllers\Controller;
use App\Http\Controllers\ImageController;
use App\Models\Post;
use App\Models\PostImage;
use App\Models\PostVideo;
use App\models\User;
use App\Models\Friend;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Cloudinary\Api\Upload\UploadApi;
use Cloudinary\Configuration\Configuration;

class PostController extends Controller
{
    public function create()
    {
        return view('post.create');
    }

    public function store(Request $request)
    {
        try {
            // Validate dữ liệu đầu vào
            $validatedData = $request->validate([
                'content'   => 'required|max:2000',
                'images.*'  => 'image|mimes:webp,jpeg,png,jpg,gif|max:2048',
                'videos.*'  => 'mimes:mp4,avi,mov|max:20480'
            ]);

            // Tạo bài đăng mới
            $post = new Post();
            $post->user_id = auth()->id();
            $post->content = $validatedData['content'];
            $post->save();

            // Xử lý upload ảnh
            if ($request->hasFile('images')) {

                $imageController = new ImageController();
                $imageUrls = $imageController->uploadMultiple($request->file('images'));

                if (empty($imageUrls) || !is_array($imageUrls)) {

                    return redirect()->back()->with('error', 'Lỗi: Không upload được ảnh nào.');
                }

                foreach ($imageUrls as $url) {
                    PostImage::create([
                        'post_id'   => $post->id,
                        'image_url' => $url
                    ]);
                }
            }

            // Xử lý upload video
            if ($request->hasFile('videos')) {
                $videoUrls = $this->uploadMedia($request->file('videos'), 'post_videos', 'video');
                foreach ($videoUrls as $url) {
                    PostVideo::create([
                        'post_id'   => $post->id,
                        'video_url' => $url
                    ]);
                }
            }

            $postOwner = Auth::user();
            $fullName = trim($postOwner->first_name . ' ' . $postOwner->last_name);
            $message = $fullName . ' vừa đăng một bài viết mới.';
            $postUrl = '/social-network/public/post/' . $post->id . '/detail'; // Sử dụng $post->id

            // Lấy danh sách bạn bè của người đăng bài
            $friends = Friend::where('user_id', Auth::id())
                ->orWhere('friend_id', Auth::id())
                ->with(['user', 'friend'])
                ->get()
                ->map(function ($friend) {
                    return $friend->user_id === Auth::id() ? $friend->friend : $friend->user;
                });
            foreach ($friends as $friend) {
                event(new NewNotificationEvent($friend->id, [
                    'message' => $message,
                    'url' => $postUrl,
                    'type' => 'create_post', // Thêm loại thông báo
                ]));
            }

            return redirect()->route('home')->with('success', 'Bài đăng đã được tạo thành công');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Lỗi khi tạo bài đăng: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $post = Post::with(['images', 'videos', 'user'])->findOrFail($id);
        return view('post.show', compact('post'));
    }

    public function edit($id)
    {
        $post = Post::with(['images', 'videos', 'user'])->findOrFail($id);
        return view('post.edit', compact('post'));
    }

    public function update(Request $request, Post $post)
    {
//        dd($request->all());

        if (!$post || !$post->exists) {
            return redirect()->back()->with('error', 'Bài đăng không tồn tại');
        }

        DB::beginTransaction(); // Đảm bảo ko lỗi giữa chừng

        try {
            // Validate dữ liệu đầu vào
            $validatedData = $request->validate([
                'content'       => 'required|max:2000',
                'images.*'      => 'image|mimes:webp,jpeg,png,jpg,gif|max:2048',
                'videos.*'      => 'mimes:mp4,avi,mov|max:20480',
                'remove_images' => 'nullable|string', // Thêm nullable
                'remove_videos' => 'nullable|string',  // Mảng chứa ID video cần xóa
            ]);

            // CẬP NHẬT
            $post->content = $validatedData['content'];
            $post->save();

            $removeImages = json_decode($request->remove_images, true) ?? [];
            $removeVideos = json_decode($request->remove_videos, true) ?? [];


            // XÓA ẢNH nếu có
            if (!empty($removeImages)) {
                foreach ($removeImages as $imageId) {
                    $image = PostImage::find($imageId);
                    if ($image) {
                        $publicId = "post_images/" . pathinfo($image->image_url, PATHINFO_FILENAME);

                        // Xóa ảnh trên Cloudinary
                        if (!$this->deleteFromCloudinary($publicId, 'image')) {
                            \Log::error("Không thể xóa ảnh trên Cloudinary: $publicId");
                        }

                        // Xóa trong database
                        $image->delete();
                    }
                }
            }

            // XÓA VIDEO CŨ nếu có
            if (!empty($removeVideos)) {
                foreach ($removeVideos as $videoId) {
                    $video = PostVideo::find($videoId);
                    if ($video) {
                        $publicId = "post_videos/" . pathinfo($video->video_url, PATHINFO_FILENAME);

                        if (!$this->deleteFromCloudinary($publicId, 'video')) {
                            \Log::error("Không thể xóa video trên Cloudinary: $publicId");
                        }

                        $video->delete();
                    }
                }
            }

            // THÊM ẢNH MỚI
            if ($request->hasFile('images')) {
                $imageController = new ImageController();
                $imageUrls = $imageController->uploadMultiple($request->file('images'));

                foreach ($imageUrls as $url) {
                    PostImage::create([
                        'post_id'   => $post->id,
                        'image_url' => $url
                    ]);
                }
            }

            // THÊM VIDEO MỚI
            if ($request->hasFile('videos')) {
                $videoUrls = $this->uploadMedia($request->file('videos'), 'post_videos', 'video');
                foreach ($videoUrls as $url) {
                    PostVideo::create([
                        'post_id'   => $post->id,
                        'video_url' => $url
                    ]);
                }
            }

            DB::commit(); // Hoàn tất transaction

            return redirect()->route('post.show', $post)->with('success', 'Bài đăng đã được cập nhật');
        } catch (\Exception $e) {
            DB::rollBack(); // Hoàn tác nếu có lỗi
            \Log::error("Lỗi khi cập nhật bài đăng: " . $e->getMessage());
            return redirect()->back()->with('error', 'Lỗi khi cập nhật bài đăng: ' . $e->getMessage());
        }
    }

    public function destroy(Post $post)
    {
        try {
            // Xóa tất cả ảnh của bài viết
            foreach ($post->images as $image) {
                $publicId = "post_images/" . pathinfo($image->image_url, PATHINFO_FILENAME);

                if (!$this->deleteFromCloudinary($publicId, 'image')) {
                    \Log::error("Không thể xóa ảnh trên Cloudinary: $publicId");
                }
                $image->delete();
            }

            // Xóa tất cả video của bài viết
            foreach ($post->videos as $video) {
                $publicId = "post_videos/" .pathinfo($video->video_url, PATHINFO_FILENAME);
                if (!$this->deleteFromCloudinary($publicId, 'video')) {
                    \Log::error("Không thể xóa video trên Cloudinary: $publicId");
                }

                $video->delete();
            }

            // Xóa bài viết
            $post->delete();

            return redirect()->route('home')->with('success', 'Bài viết đã bị xóa.');
        } catch (\Exception $e) {
            \Log::error("Lỗi khi xóa bài viết: " . $e->getMessage());
            return redirect()->back()->with('error', 'Lỗi khi xóa bài viết.');
        }
    }



    //Hàm upload ảnh/video lên Cloudinary
    private function uploadMedia($files, $folder, $resourceType = 'image')
    {
        Configuration::instance([
            'cloud' => [
                'cloud_name' => config('cloudinary.cloud_name'),
                'api_key'    => config('cloudinary.api_key'),
                'api_secret' => config('cloudinary.api_secret'),
            ],
        ]);

        $uploadApi = new UploadApi();
        $urls = [];

        foreach ($files as $file) {
            $uploadResponse = $uploadApi->upload(
                $file->getRealPath(),
                [
                    'folder'        => $folder,
                    'resource_type' => $resourceType
                ]
            );
            $urls[] = $uploadResponse['secure_url'];
        }

        return $urls;
    }

    //Hàm xóa ảnh/video trên Cloudinary
    private function deleteFromCloudinary($publicId, $resourceType = 'video')
    {
        $apiKey = config('cloudinary.api_key');
        $apiSecret = config('cloudinary.api_secret');
        $cloudName = config('cloudinary.cloud_name');
        $timestamp = time();

        // Tạo signature đúng cách (bao gồm public_id)
        $signatureParams = [
            'public_id' => $publicId,
            'timestamp' => $timestamp
        ];

        // Sắp xếp và tạo chuỗi signature
        ksort($signatureParams);
        $signatureString = '';
        foreach ($signatureParams as $key => $value) {
            $signatureString .= $key . '=' . $value . '&';
        }
        $signatureString = rtrim($signatureString, '&');
        $signature = sha1($signatureString . $apiSecret);

        // URL API destroy
        $url = "https://api.cloudinary.com/v1_1/{$cloudName}/{$resourceType}/destroy";

        // Sử dụng phương thức POST thay vì DELETE
        $response = Http::post($url, [
            'public_id' => $publicId,
            'api_key' => $apiKey,
            'timestamp' => $timestamp,
            'signature' => $signature
        ]);

        Log::info("Xóa tài nguyên từ Cloudinary: " . $publicId);
        Log::info("Kết quả: " . json_encode($response->json()));

        return $response->successful();
    }

    public function shareForm($id)
    {
        $originalPost = Post::with('user')->findOrFail($id);
        return view('post.share', compact('originalPost'));
    }

    public function share(Request $request, $id)
    {
        $originalPost = Post::findOrFail($id);

        $sharedPost = new Post([
            'user_id' => auth()->id(),
            'content' => $request->input('content'),
            'shared_post_id' => $originalPost->id,
        ]);

        $sharedPost->save();

        return redirect()->route('home')->with('success', 'Chia sẻ bài viết thành công!');
    }

    public function getLikes(Post $post)
    {
        $likingUsers = User::whereHas('likedPosts', function ($query) use ($post) {
            $query->where('post_id', $post->id);
        })->select('first_name', 'last_name', 'id')->get(); // Lấy tên và ID của người dùng thích

        return response()->json($likingUsers);
    }

}
