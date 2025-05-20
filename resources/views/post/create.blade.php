{{--create.blade.php--}}
@extends('layouts.app')

@push('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    @vite(['resources/css/create.css'])
@endpush

@push('scripts')
    @vite(['resources/js/create.js'])
@endpush

@section('content')
    <div class="container mt-5">

        <div class="post-creation-card">
            <h1 class="text-center mb-4">Tạo Bài Đăng Mới</h1>

            <form action="{{ route('post.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="form-group">
                    <label for="content">Nội Dung Bài Đăng</label>
                    <textarea
                        name="content"
                        id="content"
                        class="form-control @error('content') is-invalid @enderror"
                        rows="5"
                        placeholder="Chia sẻ điều gì đó..."
                        required
                    >{{ old('content') }}</textarea>

                    @error('content')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="images">Ảnh</label>
                    <input
                        type="file"
                        name="images[]"
                        id="images"
                        class="form-control-file @error('images') is-invalid @enderror"
                        multiple
                        accept="image/*"
                    >
                    <div id="image-preview" class="mt-2"></div>
                    @error('images')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="videos">Video</label>
                    <input
                        type="file"
                        name="videos[]"
                        id="videos"
                        class="form-control-file @error('videos') is-invalid @enderror"
                        multiple
                        accept="video/*"
                    >
                    <div id="video-preview" class="mt-2"></div>
                    @error('videos')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                    @enderror
                </div>

                <div class="form-group text-center">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="fas fa-paper-plane me-2"></i> Đăng Bài
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
