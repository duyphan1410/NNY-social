@extends('layouts.app')

@section('title', 'Chia sẻ bài viết')

@push('styles')
    @vite(['resources/css/home.css'])
@endpush

@push('styles')
    <style>
        h2 {
            font-size: 2rem;
            color: #333;
            margin-bottom: 20px;
            text-align: center;
        }

        .share-container {
            max-width: 600px;
            margin: 30px auto;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            padding: 30px; /* Tăng padding */

        }

        .original-post {
            background-color: #f9f9f9; /* Màu nền nhạt hơn */
            padding: 15px;
            border: 1px solid #eee; /* Đường viền nhạt hơn */
            border-radius: 10px;
            margin-bottom: 30px; /* Tăng margin-bottom */
        }



        .share-textarea {
            width: 100%;
            padding: 12px; /* Tăng padding */
            border-radius: 8px;
            border: 1px solid #ccc;
            resize: vertical;
            margin-bottom: 20px; /* Tăng margin-bottom */
            font-size: 1rem; /* Có thể tăng kích thước font */
        }

        .btn-share-container{
            text-align: center;
        }

        .btn-share {
            background-color: #1877f2;
            color: #fff;
            border: none;
            padding: 12px 24px; /* Tăng padding */
            border-radius: 8px;
            cursor: pointer;
            font-size: 1rem; /* Có thể tăng kích thước font */
            /* float: right; /* Thử căn phải nút chia sẻ */
        }

        .btn-share:hover {
            background-color: #145ec1;
        }
    </style>
@endpush

@section('content')
    <div class="share-container">
        <h2>Chia sẻ bài viết</h2>

        <!-- Form chia sẻ -->
        <form method="POST" action="{{ route('post.share', ['id' => $originalPost->id]) }}">
            @csrf
            <label for="content">Bạn nghĩ gì khi chia sẻ bài viết này?</label>
            <textarea name="content" rows="4" placeholder="Viết cảm nghĩ của bạn..." class="share-textarea"></textarea>


        <!-- Bài viết gốc, sử dụng layout .post như ở home.blade -->
        <div class="post original-post">
            <div class="post-header">
                <div class="user-info">
                    <img class="post-avatar" src="{{ $originalPost->user->avatar ?? '/default-avatar.png' }}" alt="avatar">
                    <div class="user-details">
                        <p class="user-name">{{ $originalPost->user->first_name }} {{ $originalPost->user->last_name }}</p>
                        <p class="post-time">{{ $originalPost->created_at->diffForHumans() }}</p>
                    </div>
                </div>
            </div>

            <div class="post-content">
                <p class="post-text">{{ $originalPost->content }}</p>
            </div>

            @php
                $mediaCount = ($originalPost->images->count() ?? 0) + ($originalPost->videos->count() ?? 0);
                $mediaClass = match(true) {
                    $mediaCount === 1 => 'single',
                    $mediaCount === 2 => 'two',
                    $mediaCount === 3 => 'three',
                    $mediaCount >= 4 => 'four',
                    default => '',
                };
            @endphp

            @if ($mediaCount > 0)
                <div class="post-media {{ $mediaClass }}">
                    @foreach ($originalPost->images as $image)
                        <img src="{{ $image->image_url }}" alt="Ảnh bài viết" class="post-image">
                    @endforeach
                    @foreach ($originalPost->videos as $video)
                        <video controls class="post-image">
                            <source src="{{ $video->video_url }}" type="video/mp4">
                        </video>
                    @endforeach
                </div>
            @endif
        </div>
            <div class="btn-share-container">
                <button type="submit" class="btn-share">Chia sẻ</button>
            </div>
        </form>
    </div>
@endsection
