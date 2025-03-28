<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trang chủ</title>
    <!-- Kết nối file -->
    @vite(['resources/css/detail.css'])
    @vite(['resources/js/detail.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">


</head>
<body>
@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="post col-9">
            <!-- Header: User info and timestamp -->
            <div class="post-header">
                <div class="user-info">
                    <img class="post-avatar" src="{{ $post->user->avatar }}" alt="{{ $post->user->name }}">
                    <div class="user-details">
                        <h4 class="user-name">{{ $post->user->first_name }} {{ $post->user->last_name }}</h4>
                        <span class="post-time">{{ $post->created_at->diffForHumans() }}</span>
                    </div>
                </div>
                <div class="post-options dropdown">
                    <button class="dropdown-btn">
                        <i class="fas fa-ellipsis-h"></i>
                    </button>
                    <ul class="dropdown-menu">
                        @if(auth()->check() && auth()->id() == $post->user_id)
                            <li>
                                <a href="{{ route('post.edit', $post->id) }}">
                                    <i class="fas fa-edit"></i> Chỉnh sửa
                                </a>
                            </li>
                            <li>
                                <form action="{{ route('post.destroy', $post->id) }}" method="POST" class="delete-form">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" onclick="return confirm('Bạn có chắc chắn muốn xóa?')">
                                        <i class="fas fa-trash"></i> Xóa
                                    </button>
                                </form>
                            </li>
                            <li>
                                <button class="hide-post-btn" data-post-id="{{ $post->id }}">
                                    <i class="fas fa-eye-slash"></i> Ẩn bài viết
                                </button>
                            </li>
                        @else
                            <li>
                                <button class="report-post-btn" data-post-id="{{ $post->id }}">
                                    <i class="fas fa-flag"></i> Báo cáo
                                </button>
                            </li>
                        @endif
                    </ul>
                </div>
            </div>

            <!-- Post content -->
            <div class="post-content">
                <p class="post-text">{{ $post->content }}</p>
            </div>

            <!-- Post media (Images & Videos combined) -->
            @php
                $media = collect([]);
                if ($post->images) {
                    $media = $media->merge($post->images);
                }
                if ($post->videos) {
                    $media = $media->merge($post->videos);
                }
                $mediaCount = $media->count();
                $mediaClass = $mediaCount == 1 ? 'single' : ($mediaCount == 2 ? 'two' : ($mediaCount == 3 ? 'three' : 'four'));
            @endphp

            @if ($mediaCount > 0)
                <div class="post-media {{ $mediaClass }}">
                    @foreach ($media as $item)
                        @if (isset($item->image_url))
                            <img class="post-image" src="{{ $item->image_url }}" alt="Post Image">
                        @elseif (isset($item->video_url))
                            <video class="post-video" controls>
                                <source src="{{ $item->video_url }}" type="video/mp4">
                                Your browser does not support the video tag.
                            </video>
                        @endif
                    @endforeach
                </div>
            @endif

            <!-- Post interactions -->
            <div class="post-stats">
                <div class="like-count">
                    <i class="fas fa-heart"></i>
                    <span>{{ $post->likes_count ?? 0 }}</span>
                </div>
                <div class="comment-count">
                    <span>{{ $post->comments_count ?? 0 }} bình luận</span>
                </div>
            </div>

            <!-- Post actions -->
            <div class="post-actions">
                <button class="action-btn like-btn {{ $post->user_has_liked ? 'active' : '' }}" data-post-id="{{ $post->id }}">
                    <i class="fas fa-heart"></i>
                    <span>Thích</span>
                </button>
                <button class="action-btn comment-btn" data-post-id="{{ $post->id }}">
                    <i class="fas fa-comment"></i>
                    <span>Bình luận</span>
                </button>
                <button class="action-btn share-btn" data-post-id="{{ $post->id }}">
                    <i class="fas fa-share"></i>
                    <span>Chia sẻ</span>
                </button>
            </div>

{{--            <!-- Comments section -->--}}
{{--            <div class="post-comments">--}}
{{--                @if ($post->comments && count($post->comments) > 0)--}}
{{--                    @foreach ($post->comments as $comment)--}}
{{--                        <div class="comment">--}}
{{--                            <img class="comment-avatar" src="{{ $comment->user->avatar }}" alt="{{ $comment->user->name }}">--}}
{{--                            <div class="comment-content">--}}
{{--                                <div class="comment-user">{{ $comment->user->first_name }} {{ $comment->user->last_name }}</div>--}}
{{--                                <div class="comment-text">{{ $comment->content }}</div>--}}
{{--                                <div class="comment-actions">--}}
{{--                                    <span class="comment-time">{{ $comment->created_at->diffForHumans() }}</span>--}}
{{--                                    <button class="comment-like-btn">Thích</button>--}}
{{--                                    <button class="comment-reply-btn">Trả lời</button>--}}
{{--                                </div>--}}
{{--                            </div>--}}
{{--                        </div>--}}
{{--                    @endforeach--}}
{{--                @endif--}}

{{--                <!-- Comment form -->--}}
{{--                <div class="comment-form">--}}
{{--                    <img class="comment-form-avatar" src="{{ auth()->user()->avatar }}" alt="{{ auth()->user()->name }}">--}}
{{--                    <form action="{{ route('comments.store') }}" method="POST" class="comment-input-container">--}}
{{--                        @csrf--}}
{{--                        <input type="hidden" name="post_id" value="{{ $post->id }}">--}}
{{--                        <input type="text" name="content" class="comment-input" placeholder="Viết bình luận...">--}}
{{--                        <button type="submit" class="comment-submit">--}}
{{--                            <i class="fas fa-paper-plane"></i>--}}
{{--                        </button>--}}
{{--                    </form>--}}
{{--                </div>--}}
{{--            </div>--}}
        </div>

    </div>
@endsection

</body>

</html>
