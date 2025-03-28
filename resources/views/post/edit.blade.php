<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trang chủ</title>
    <!-- Kết nối file -->
    @vite(['resources/css/edit.css'])
    @vite(['resources/js/edit.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">


</head>
<body>
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
                <div class="current-images" id="image-preview">
                    @foreach($post->images as $image)
                        <div id="current-image-{{ $image->id }}">
                            <img src="{{ $image->image_url }}" style="width: 100px; height: auto;">
                            <button type="button" class="remove-current-image" data-id="{{ $image->id }}">×</button>
                        </div>
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
                <div class="current-videos" id="video-preview">
                    @foreach($post->videos as $video)
                        <div id="current-video-{{ $video->id }}">
                            <video controls style="width: 200px;">
                                <source src="{{ $video->video_url }}" type="video/mp4">
                            </video>
                            <button type="button" class="remove-current-video" data-id="{{ $video->id }}">×</button>
                        </div>
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
