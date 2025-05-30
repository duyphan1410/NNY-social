@extends('layouts.app')

@push('styles')
    @vite(['resources/css/home.css'])
    @vite(['resources/css/detail.css'])
@endpush

@push('scripts')
    @vite(['resources/js/home.js'])
    @vite(['resources/js/detail.js'])
@endpush

@php
    // Tách riêng xử lý hình ảnh và video
    $images = collect($post->images ?? []);
    $videos = collect($post->videos ?? []);

    // Giới hạn số lượng theo yêu cầu
    $imagesToShow = $images->take(5);
    $videosToShow = $videos->take(3);

    // Đếm số lượng thực tế
    $imageCount = $imagesToShow->count();
    $videoCount = $videosToShow->count();
    $totalMediaCount = $imageCount + $videoCount;

    // Xác định class cho layout dựa trên số lượng phương tiện
    $layoutClass = '';
    if ($totalMediaCount == 1) {
        $layoutClass = 'media-single';
    } elseif ($totalMediaCount == 2) {
        $layoutClass = 'media-two';
    } elseif ($totalMediaCount == 3) {
        $layoutClass = 'media-three';
    } elseif ($totalMediaCount == 4) {
        $layoutClass = 'media-four';
    } elseif ($totalMediaCount >= 5) {
        $layoutClass = 'media-five';
    }
@endphp

@section('content')
    <div class="container">
        <div class="post col-9">
            <!-- Header: User info and timestamp -->
            @php
                $isOwner = auth()->check() && auth()->id() === $post->user->id;
                $profileRoute = $isOwner
                    ? route('profile.me')
                    : route('profile.show', $post->user->id);
            @endphp
            <div class="post-header">
                <div class="user-info">
                    <a href="{{ $profileRoute }}">
                        <img class="post-avatar" src="{{ $post->user->avatar }}" alt="{{ $post->user->name }}">
                        <div class="user-details">
                            <h4 class="user-name">{{ $post->user->first_name }} {{ $post->user->last_name }}</h4>
                            <a href="{{ route('post.show', ['id' => $post->id]) }}"><span class="post-time">{{ $post->created_at->diffForHumans() }}</span></a>
                        </div>
                    </a>
                </div>
                <div class="post-options dropdown">
                    <button class="dropdown-btn">
                        <i class="fas fa-ellipsis-h"></i>
                    </button>
                    <ul class="dropdown-menu">
                        @if(auth()->check() && auth()->id() == $post->user_id)
                            <li>
                                <a href="{{ route('post.edit', ['id' => $post->id]) }}">
                                    <i class="fas fa-edit"></i> Chỉnh sửa
                                </a>
                            </li>
                            <li>
                                <form action="{{ route('post.destroy', $post) }}" method="POST" class="delete-form">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="delete-btn">
                                        <i class="fas fa-trash"></i> Xóa
                                    </button>
                                </form>
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

            <div class="post-content">
                <p class="post-text">{{ $post->content }}</p>
            </div>

            @if ($post->sharedPost)
                @php
                    $isOwner = auth()->check() && auth()->id() === $post->sharedPost->user->id;
                    $profileRoute = $isOwner
                        ? route('profile.me')
                        : route('profile.show', $post->sharedPost->user->id);
                @endphp
                <div class="shared-post">
                    <div class="post-header">
                        <div class="user-info">
                            <a href="{{ $profileRoute }}">
                                <img class="post-avatar" src="{{ $post->sharedPost->user->avatar }}" alt="{{ $post->sharedPost->user->name }}">
                                <div class="user-details">
                                    <h4 class="user-name">{{ $post->sharedPost->user->first_name }} {{ $post->sharedPost->user->last_name }}</h4>
                                    <a href="{{ route('post.show', ['id' => $post->sharedPost->id]) }}"><span class="post-time">{{ $post->sharedPost->created_at->diffForHumans() }}</span></a>
                                </div>
                            </a>
                        </div>
                    </div>
                    <div class="post-content">
                        <p class="post-text">{{ $post->sharedPost->content }}</p>
                    </div>
                    @php
                        $sharedMedia = collect([]);
                        if ($post->sharedPost->images) {
                            $sharedMedia = $sharedMedia->merge($post->sharedPost->images);
                        }
                        if ($post->sharedPost->videos) {
                            $sharedMedia = $sharedMedia->merge($post->sharedPost->videos);
                        }
                        $sharedMediaCount = $sharedMedia->count();
                        $sharedMediaClass = $sharedMediaCount == 1 ? 'single' : ($sharedMediaCount == 2 ? 'two' : ($sharedMediaCount == 3 ? 'three' : 'four'));
                    @endphp

                    @if ($sharedMediaCount > 0)
                        <div class="post-media {{ $sharedMediaClass }}">
                            @foreach ($sharedMedia as $item)
                                @if (isset($item->image_url))
                                    <img class="post-image" src="{{ $item->image_url }}" alt="Shared Post Image">
                                @elseif (isset($item->video_url))
                                    <video class="post-video" controls>
                                        <source src="{{ $item->video_url }}" type="video/mp4">
                                        Your browser does not support the video tag.
                                    </video>
                                @endif
                            @endforeach
                        </div>
                    @endif
                </div>
            @else
                <!-- Hiển thị phương tiện -->
                @if ($totalMediaCount > 0)
                    <div class="post-media-container {{ $layoutClass }}">
                        <!-- Hiển thị video (ưu tiên hiển thị trước) -->
                        @foreach ($videosToShow as $index => $video)
                            <div class="media-item video-item">
                                <video class="post-video" controls>
                                    <source src="{{ $video->video_url }}" type="video/mp4">
                                    Your browser does not support the video tag.
                                </video>
                                <div class="media-overlay">
                                    <i class="fas fa-play-circle"></i>
                                </div>
                            </div>
                        @endforeach

                        <!-- Hiển thị hình ảnh -->
                        @foreach ($imagesToShow as $index => $image)
                            <div class="media-item image-item">
                                <img class="post-image" src="{{ $image->image_url }}" alt="Post Image {{ $index + 1 }}">

                                <!-- Nếu là ảnh cuối cùng và còn ảnh chưa hiển thị -->
                                @if ($index == 4 && $images->count() > 5)
                                    <div class="more-overlay">
                                        <span>+{{ $images->count() - 5 }}</span>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif
            @endif

            <div class="post-stats">
                <div class="like-count">
                    <button class="view-likes-btn" data-post-id="{{ $post->id }}">
                        <span>{{ $post->likes_count ?? 0 }} thích</span>
                    </button>
                </div>

                <div id="likes-modal" class="modal" style="display: none;">
                    <div class="modal-content">
                        <span class="close-button">&times;</span>
                        <h3>Những người đã thích</h3>
                        <ul id="likes-list">
                        </ul>
                    </div>
                </div>

                <div class="comment-count">
                    <span>{{ $post->comments_count ?? 0 }} bình luận</span>
                </div>
            </div>

            <div class="post-actions">
                <button class="action-btn like-btn {{ $post->user_has_liked ? 'active' : '' }}" data-post-id="{{ $post->id }}">
                    <i class="fas fa-heart"></i>
                    <span class="like-count">{{ $post->likes_count }}</span>
                    <span>Thích</span>
                </button>
                <span class="like-float" style="display:none;">❤️</span>
                <button class="action-btn comment-btn" data-post-id="{{ $post->id }}">
                    <i class="fas fa-comment"></i>
                    <span>Bình luận</span>
                </button>
                <a href="{{ route('post.share.form', ['id' => $post->id]) }}" class="action-btn share-btn">
                    <i class="fas fa-share"></i>
                    <span>Chia sẻ</span>
                </a>
            </div>
            <div class="post">
                <div class="post-comments" id="comments-{{ $post->id }}">
                    <div class="comment-list">
                        @foreach ($post->comments as $comment)
                            @php
                                $isOwner = auth()->check() && auth()->id() === $comment->user->id;
                                $profileRoute = $isOwner
                                    ? route('profile.me')
                                    : route('profile.show', $comment->user->id);
                            @endphp
                            <div class="comment">
                                <div class="comment-avatar-container">
                                    <a href="{{ $profileRoute }}">
                                        <img class="comment-avatar" src="{{ $comment->user->avatar ?? '/default-avatar.png' }}" alt="{{ $comment->user->first_name }} {{ $comment->user->last_name }}">
                                    </a>
                                </div>
                                <div class="comment-content-container">
                                    <div class="comment-bubble">
                                        <a href="{{ $profileRoute }}" class="comment-author">{{ $comment->user->first_name }} {{ $comment->user->last_name }}</a>
                                        <div class="comment-text">{{ $comment->content }}</div>
                                    </div>
                                    <div class="comment-actions-row">
                                        <span class="comment-time">{{ $comment->created_at->diffForHumans() }}</span>
                                        @if (auth()->check() && !$isOwner)
                                            <button
                                                class="reply-btn"
                                                data-author="{{ $comment->user->first_name }} {{ $comment->user->last_name }}"
                                                data-user-id="{{ $comment->user->id }}"
                                                data-comment-id="{{ $comment->id }}"
                                            >
                                                Trả lời
                                            </button>
                                        @endif
                                        @if ($isOwner)
                                            <form method="POST" action="{{ route('post.comment.destroy', ['id' => $comment->id]) }}" class="inline-form">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="comment-delete-btn" onclick="return confirm('Bạn có chắc chắn muốn xóa bình luận này?')">Xóa</button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <form method="POST" action="{{ route('post.comment.store', ['id' => $post->id]) }}" class="comment-form">
                        @csrf
                        <div class="comment-input-container">
                            @if(auth()->check())
                                <img class="comment-form-avatar" src="{{ auth()->user()->avatar ?? '/default-avatar.png' }}" alt="{{ auth()->user()->first_name }} {{ auth()->user()->last_name }}">
                            @else
                                <img class="comment-form-avatar" src="/default-avatar.png" alt="Guest">
                            @endif
                            <div class="comment-input-wrapper">
                                <textarea class="comment-input" name="content" rows="1" required placeholder="Viết bình luận..."></textarea>
                                <input type="hidden" name="parent_comment_id" id="parent_comment_id" value="">
                                <button type="submit" class="comment-submit-btn">
                                    <i class="fas fa-paper-plane"></i>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
