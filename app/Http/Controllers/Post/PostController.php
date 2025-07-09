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

    public function store(Request $request) {
        try {
            // Validate dữ liệu đầu vào
            $validatedData = $request->validate([
                'content' => 'required|max:5000',
                'image-data' => 'nullable|json',
                'video-data' => 'nullable|json'
            ]);

            // Tạo bài đăng mới
            $post = new Post();
            $post->user_id = auth()->id();
            $post->content = $validatedData['content'];
            $post->save();

            // Xử lý ảnh đã upload
            if (!empty($validatedData['image-data'])) {
                $imageData = json_decode($validatedData['image-data'], true);

                if (is_array($imageData)) {
                    foreach ($imageData as $item) {
                        if (isset($item['url']) && isset($item['public_id'])) {
                            PostImage::create([
                                'post_id' => $post->id,
                                'image_url' => $item['url'],
                                'public_id' => $item['public_id'],
                            ]);
                        }
                    }
                }
            }

            // Xử lý video đã upload
            if (!empty($validatedData['video-data'])) {
                $videoData = json_decode($validatedData['video-data'], true);

                if (is_array($videoData)) {
                    foreach ($videoData as $item) {
                        if (isset($item['url']) && isset($item['public_id'])) {
                            PostVideo::create([
                                'post_id' => $post->id,
                                'video_url' => $item['url'],
                                'public_id' => $item['public_id'],
                            ]);
                        }
                    }
                }
            }

            $postOwner = Auth::user();
            $fullName = trim($postOwner->first_name . ' ' . $postOwner->last_name);
            $message = $fullName . ' vừa đăng một bài viết mới.';
            $postUrl = '/social-network/public/post/' . $post->id . '/detail';

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
                    'type' => 'create_post',
                ]));
            }

            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'success',
                    'redirect' => route('home'),
                    'message' => 'Bài đăng đã được tạo thành công'
                ]);
            }

            return redirect()->route('home')->with('success', 'Bài đăng đã được tạo thành công');


        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Lỗi khi tạo bài đăng: ' . $e->getMessage()
                ], 500);
            }

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
        if (!$post || !$post->exists) {
            $message = 'Bài đăng không tồn tại';
            return $request->expectsJson()
                ? response()->json(['status' => 'error', 'message' => $message], 404)
                : redirect()->back()->with('error', $message);
        }

        DB::beginTransaction();

        try {
            $validatedData = $request->validate([
                'content'       => 'required|max:2000',
                'image-data' => 'nullable|json',
                'video-data' => 'nullable|json',
                'remove_images' => 'nullable|string',
                'remove_videos' => 'nullable|string',
            ]);

            $post->content = $validatedData['content'];
            $post->save();

            $removeImages = $request->filled('remove_images')
                ? explode(',', $request->remove_images)
                : [];

            $removeVideos = $request->filled('remove_videos')
                ? explode(',', $request->remove_videos)
                : [];


            // XÓA ẢNH
            if (!empty($removeImages)) {
                foreach ($removeImages as $imageId) {
                    $image = PostImage::find($imageId);
                    if ($image) {
                        $publicId = $image->public_id;
                        if (!$this->deleteFromCloudinary($publicId, 'image')) {
                            \Log::error("Không thể xóa ảnh trên Cloudinary: $publicId");
                        }
                        $image->delete();
                    }
                }
            }

            // XÓA VIDEO
            if (!empty($removeVideos)) {
                foreach ($removeVideos as $videoId) {
                    $video = PostVideo::find($videoId);
                    if ($video) {
                        $publicId = $video->public_id;
                        if (!$this->deleteFromCloudinary($publicId, 'video')) {
                            \Log::error("Không thể xóa video trên Cloudinary: $publicId");
                        }
                        $video->delete();
                    }
                }
            }

//            // THÊM ẢNH MỚI
//            if ($request->hasFile('images')) {
//                $imageController = new ImageController();
//                $imageItems = $imageController->uploadMultiple($request->file('images'));
//                foreach ($imageItems as $item) {
//                    PostImage::create([
//                        'post_id'   => $post->id,
//                        'image_url' => $item['url'],
//                        'public_id' => $item['public_id']
//                    ]);
//                }
//            }
//
//            // THÊM VIDEO MỚI
//            if ($request->hasFile('videos')) {
//                $videoItems = $this->uploadMedia($request->file('videos'), 'post_videos', 'video');
//                foreach ($videoItems as $item) {
//                    PostVideo::create([
//                        'post_id'   => $post->id,
//                        'video_url' => $item['url'],
//                        'public_id' => $item['public_id']
//                    ]);
//                }
//            }

            // XỬ LÝ ẢNH MỚI TỪ CLOUDINARY (unsigned)
            if (!empty($request->input('image-data'))) {
                $imageData = json_decode($request->input('image-data'), true);
                if (is_array($imageData)) {
                    foreach ($imageData as $item) {
                        if (isset($item['url']) && isset($item['public_id'])) {
                            PostImage::create([
                                'post_id' => $post->id,
                                'image_url' => $item['url'],
                                'public_id' => $item['public_id'],
                            ]);
                        }
                    }
                }
            }

            // XỬ LÝ VIDEO MỚI TỪ CLOUDINARY (unsigned)
            if (!empty($request->input('video-data'))) {
                $videoData = json_decode($request->input('video-data'), true);
                if (is_array($videoData)) {
                    foreach ($videoData as $item) {
                        if (isset($item['url']) && isset($item['public_id'])) {
                            PostVideo::create([
                                'post_id' => $post->id,
                                'video_url' => $item['url'],
                                'public_id' => $item['public_id'],
                            ]);
                        }
                    }
                }
            }

            DB::commit();

            // ✅ Trả về JSON nếu là request AJAX
            if ($request->expectsJson()) {
                return response()->json([
                    'status'   => 'success',
                    'message'  => 'Bài đăng đã được cập nhật',
                    'redirect' => route('post.show', $post)
                ]);
            }

            return redirect()->route('post.show', $post)->with('success', 'Bài đăng đã được cập nhật');
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error("Lỗi khi cập nhật bài đăng: " . $e->getMessage());

            $message = 'Lỗi khi cập nhật bài đăng: ' . $e->getMessage();

            return $request->expectsJson()
                ? response()->json(['status' => 'error', 'message' => $message], 500)
                : redirect()->back()->with('error', $message);
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
        $mediaList = [];

        foreach ($files as $file) {
            $uploadResponse = $uploadApi->upload(
                $file->getRealPath(),
                [
                    'folder'        => $folder,
                    'resource_type' => $resourceType
                ]
            );
            $mediaList[] = [
                'url' => $uploadResponse['secure_url'],
                'public_id' => $uploadResponse['public_id'], // Lưu cái này
            ];
        }

        return $mediaList;
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
