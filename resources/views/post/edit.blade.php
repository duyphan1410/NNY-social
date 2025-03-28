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

            <input type="hidden" name="remove_images" id="remove_images">
            <input type="hidden" name="remove_videos" id="remove_videos">


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
                        <div id="current-image-{{ $image->id }}" style="position: relative; display: inline-block; margin: 5px;">
                            <img src="{{ $image->image_url }}" style="width: 100px; height: auto;">
                            <button type="button" class="remove-current-image" data-id="{{ $image->id }}"
                                    style="position: absolute; top: 5px; right: 5px; background: red; color: white; border: none; cursor: pointer; padding: 5px 10px; border-radius: 50%; font-size: 16px; z-index: 10;">
                                ×
                            </button>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="form-group">
                <label for="images">Thêm Mới Ảnh</label>
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
                        <div id="current-video-{{ $video->id }}" style="position: relative; display: inline-block; margin: 5px;">
                            <video controls style="width: 200px;" style="width: 100px; height: auto;">
                                <source src="{{ $video->video_url }}" type="video/mp4">
                            </video>
                            <button type="button" class="remove-current-video" data-id="{{ $video->id }}"
                                    style="position: absolute; top: 5px; right: 5px; background: red; color: white; border: none; cursor: pointer; padding: 5px 10px; border-radius: 50%; font-size: 16px; z-index: 10;">
                                ×
                            </button>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="form-group">
                <label for="videos">Thêm Mới Video </label>
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
