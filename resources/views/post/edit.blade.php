{{--edit.blade.php--}}
@extends('layouts.app')

@push('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    @vite(['resources/css/edit.css'])
@endpush

@push('scripts')
    @vite(['resources/js/edit.js'])
@endpush

@section('content')
    <div class="container mt-5">
        <div class="post-creation-card">
            <h1 class="text-center mb-4">Chỉnh Sửa Bài Đăng</h1>

            <form id="post-form" action="{{ route('post.update', $post) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <input type="hidden" name="remove_images" id="remove_images">
                <input type="hidden" name="remove_videos" id="remove_videos">
                <input type="hidden" name="image-data" id="image-data">
                <input type="hidden" name="video-data" id="video-data">


                <div class="form-group">
                    <label for="content">Nội Dung Bài Đăng</label>
                    <textarea
                        name="content"
                        id="content"
                        class="form-control @error('content') is-invalid @enderror"
                        rows="5"
                        placeholder="Chia sẻ điều gì đó..."
                        required
                    >{{ old('content', $post->content) }}</textarea>

                    @error('content')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                    @enderror
                </div>

                {{-- ẢNH --}}
                <div class="form-group">
                    <label for="images">Ảnh hiện tại </label>
                    <div class="upload-dropzone" id="image-dropzone">
                        <input
                            type="file"
                            name="images[]"
                            id="images"
                            class="form-control-file"
                            multiple
                            accept="image/*"
                        >
                    </div>
                    <div class="preview-container" id="current-image-preview">
                        @foreach($post->images as $image)
                            <div class="preview-item" id="current-image-{{ $image->id }}">
                                <img src="{{ $image->image_url }}" alt="Image">
                                <button type="button" class="preview-remove remove-current-image" data-id="{{ $image->id }}">×</button>
                            </div>
                        @endforeach
                    </div>
                    <div class="preview-container" id="new-image-preview"></div>
                </div>

                {{-- VIDEO --}}
                <div class="form-group">
                    <label for="videos">Video hiện tại </label>
                    <div class="upload-dropzone" id="video-dropzone">
                        <input
                            type="file"
                            name="videos[]"
                            id="videos"
                            class=" form-control-file"
                            multiple
                            accept="video/*"
                        >
                    </div>
                    <div class="preview-container" id="current-video-preview">
                        @foreach($post->videos as $video)
                            <div class="preview-item" id="current-video-{{ $video->id }}">
                                <video src="{{ $video->video_url }}" controls></video>
                                <button type="button" class="preview-remove remove-current-video" data-id="{{ $video->id }}">×</button>
                            </div>
                        @endforeach
                    </div>
                    <div class="preview-container" id="new-video-preview"></div>
                </div>


                <div class="form-group text-center">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="fas fa-paper-plane me-2"></i> Cập Nhật Bài Đăng
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
