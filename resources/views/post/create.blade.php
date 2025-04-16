@extends('layouts.app')

@push('styles')
    @vite(['resources/css/create.css'])
@endpush

@push('scripts')
    @vite(['resources/js/create.js'])
@endpush

@section('content')
    <div class="container mt-5">
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
                    placeholder="Nhập nội dung bài đăng..."
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
                <button type="submit" class="btn btn-primary btn-lg">Đăng Bài</button>
            </div>
        </form>
    </div>
@endsection
