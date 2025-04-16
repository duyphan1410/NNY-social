@extends('layouts.app')

@push('styles')
    @vite(['resources/css/home.css'])
    @vite(['resources/css/detail.css'])
@endpush

@push('scripts')
    @vite(['resources/js/home.js'])
    @vite(['resources/js/detail.js'])
@endpush

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
                            {{--                                                    <li>--}}
                            {{--                                                        <button class="hide-post-btn" data-post-id="{{ $post->id }}">--}}
                            {{--                                                            <i class="fas fa-eye-slash"></i> Ẩn bài viết--}}
                            {{--                                                        </button>--}}
                            {{--                                                    </li>--}}
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
                <p class="post-text">{{ $post->content }}</p> </div>

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
                @php
                    $originalMedia = collect([]);
                    if ($post->images) {
                        $originalMedia = $originalMedia->merge($post->images);
                    }
                    if ($post->videos) {
                        $originalMedia = $originalMedia->merge($post->videos);
                    }
                    $originalMediaCount = $originalMedia->count();
                    $originalMediaClass = $originalMediaCount == 1 ? 'single' : ($originalMediaCount == 2 ? 'two' : ($originalMediaCount == 3 ? 'three' : 'four'));
                @endphp

                @if ($originalMediaCount > 0)
                    <div class="post-media {{ $originalMediaClass }}">
                        @foreach ($originalMedia as $item)
                            @if (isset($item->image_url))
                                <img class="post-image" src="{{ $item->image_url }}" alt="Original Post Image">
                            @elseif (isset($item->video_url))
                                <video class="post-video" controls>
                                    <source src="{{ $item->video_url }}" type="video/mp4">
                                    Your browser does not support the video tag.
                                </video>
                            @endif
                        @endforeach
                    </div>
                @endif
            @endif

            <div class="post-stats">
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
                                <div class="user-info">
                                    <a href="{{ $profileRoute }}">
                                        <img class="comment-avatar" src="{{ $comment->user->avatar ?? '/default-avatar.png' }}" alt="{{ $comment->user->first_name }} {{ $comment->user->last_name }}">
                                        <div class="user-details">
                                            <span class="comment-author">{{ $comment->user->first_name }} {{ $comment->user->last_name }}</span>
                                            <span class="comment-time">{{ $comment->created_at->diffForHumans() }}</span>
                                        </div>
                                    </a>
                                </div>
                                <p class="comment-content">{{ $comment->content }}</p>
                            </div>
                        @endforeach
                    </div>

                    <form method="POST" action="{{ route('post.comment.store', ['id' => $post->id]) }}" class="comment-form">
                        @csrf
                        <textarea name="content" rows="2" required placeholder="Viết bình luận..."></textarea>
                        <button type="submit" class="comment-submit-btn">Gửi</button>
                    </form>
                </div>
            </div>
    </div>
@endsection
