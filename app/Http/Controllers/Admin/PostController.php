<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{
    // Danh sách bài đăng (có thể tìm kiếm theo tiêu đề, nội dung hoặc người đăng)
    public function index()
    {
        $posts = Post::with('user')
            ->orderByDesc('created_at')
            ->paginate(10);

        return view('admin.posts.index', compact('posts'));
    }


    public function destroy($id)
    {
        $post = Post::findOrFail($id);
        $post->delete();

        return redirect()->route('admin.posts.index')->with('success', 'Xóa bài đăng thành công.');
    }
    // Bật/tắt hiển thị bài đăng
    public function toggleVisibility($id)
    {
        $post = Post::findOrFail($id);
        $post->is_visible = !$post->is_visible;
        $post->save();

        return back()->with('success', 'Cập nhật trạng thái bài đăng thành công');
    }
}
