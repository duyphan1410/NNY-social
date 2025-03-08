@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Chỉnh Sửa Bài Đăng</h1>

        <form action="{{ route('post.update', $post) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="content">Nội Dung Bài Đăng</label>
                <textarea
                    name="content"
                    id="content"
                    class="form-control @error('content') is-invalid @enderror"
                    rows="4"
                    required
                >{{ old('content', $post->content) }}</textarea>

                @error('content')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>

            <div class="form-group">
                <label>Ảnh Hiện Tại</label>
                <div class="current-images">
                    @foreach($post->images as $image)
                        <img src="{{ $image->image_url }}" alt="Current Image" style="max-width: 200px;">
                    @endforeach
                </div>
            </div>

            <div class="form-group">
                <label for="images">Thêm Ảnh Mới</label>
                <input
                    type="file"
                    name="images[]"
                    id="images"
                    class="form-control-file"
                    multiple
                    accept="image/*"
                >
            </div>

            <div class="form-group">
                <label>Video Hiện Tại</label>
                <div class="current-videos">
                    @foreach($post->videos as $video)
                        <video controls style="max-width: 200px;">
                            <source src="{{ $video->video_url }}" type="video/mp4">
                        </video>
                    @endforeach
                </div>
            </div>

            <div class="form-group">
                <label for="videos">Thêm Video Mới</label>
                <input
                    type="file"
                    name="videos[]"
                    id="videos"
                    class="form-control-file"
                    multiple
                    accept="video/*"
                >
            </div>

            <button type="submit" class="btn btn-primary">Cập Nhật Bài Đăng</button>
        </form>
    </div>
@endsection
