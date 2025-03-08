<?php

namespace App\Http\Controllers\Post;

use App\Http\Controllers\Controller;
use App\Http\Controllers\ImageController;
use App\Models\Post;
use App\Models\PostImage;
use App\Models\PostVideo;
use Illuminate\Http\Request;
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
                'images.*'  => 'image|mimes:jpeg,png,jpg,gif|max:2048',
                'videos.*'  => 'mimes:mp4,avi,mov|max:20480'
            ]);

            // Tạo bài đăng mới
            $post = new Post();
            $post->user_id = auth()->id();
            $post->content = $validatedData['content'];
            $post->save();

            // Xử lý upload ảnh
            if ($request->hasFile('images')) {
                \Log::info('🔥 Số lượng file images nhận được:', ['count' => count($request->file('images'))]);

                $imageController = new ImageController();
                $imageUrls = $imageController->uploadMultiple($request->file('images'));

                \Log::info('✅ Số lượng URL trả về từ uploadMultiple:', ['count' => is_array($imageUrls) ? count($imageUrls) : 0, 'urls' => $imageUrls]);

                if (empty($imageUrls) || !is_array($imageUrls)) {
                    \Log::error('❌ Không upload được ảnh nào.');
                    return redirect()->back()->with('error', 'Lỗi: Không upload được ảnh nào.');
                }

                foreach ($imageUrls as $url) {
                    PostImage::create([
                        'post_id'   => $post->id,
                        'image_url' => $url
                    ]);
                    \Log::info('🖼 Ảnh đã được lưu vào database:', ['post_id' => $post->id, 'image_url' => $url]);
                }

                \Log::info('📌 Tổng số ảnh đã lưu vào database:', ['count' => count($imageUrls)]);
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

            return redirect()->route('home')->with('success', 'Bài đăng đã được tạo thành công');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Lỗi khi tạo bài đăng: ' . $e->getMessage());
        }
    }

    public function show(Post $post)
    {
        $post->load('images', 'videos', 'user');
        return view('post.show', compact('post'));
    }

    public function edit(Post $post)
    {
        if (auth()->id() !== $post->user_id) {
            abort(403, 'Bạn không có quyền chỉnh sửa bài đăng này');
        }

        $post->load('images', 'videos');
        return view('post.edit', compact('post'));
    }

    public function update(Request $request, Post $post)
    {
        try {
            // Kiểm tra quyền
            if (auth()->id() !== $post->user_id) {
                abort(403, 'Bạn không có quyền chỉnh sửa bài đăng này');
            }

            // Validate
            $validatedData = $request->validate([
                'content'  => 'required|max:2000',
                'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
                'videos.*' => 'mimes:mp4,avi,mov|max:20480'
            ]);

            // Cập nhật nội dung
            $post->content = $validatedData['content'];
            $post->save();

            // Upload ảnh mới
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

            // Upload video mới
            if ($request->hasFile('videos')) {
                $videoUrls = $this->uploadMedia($request->file('videos'), 'post_videos', 'video');
                foreach ($videoUrls as $url) {
                    PostVideo::create([
                        'post_id'   => $post->id,
                        'video_url' => $url
                    ]);
                }
            }

            return redirect()->route('post.show', $post)->with('success', 'Bài đăng đã được cập nhật');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Lỗi khi cập nhật bài đăng: ' . $e->getMessage());
        }
    }

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
}
