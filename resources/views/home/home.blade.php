@extends('layouts.app')

@section('title', 'Trang chủ')

@push('styles')
    @vite(['resources/css/home.css'])
@endpush

@push('scripts')
    @vite(['resources/js/home.js'])
@endpush

@section('content')
        @if(session('friend_accepted_' . Auth::id()))
            <p style="color: green;">{{ session('friend_accepted_' . Auth::id()) }}</p>
        @endif

        @if(session('friend_rejected_' . Auth::id()))
            <p style="color: red;">{{ session('friend_rejected_' . Auth::id()) }}</p>
        @endif
        <div class="container">

            <div class="col-3 sidebar">
                <a href="#">{{ Auth::user()->first_name }} {{ Auth::user()->last_name }}</a>
                <a href="{{ route('friend.search') }}">Tìm bạn bè</a>
                <a href="{{ route('friend.index') }}">Bạn bè</a>
                <a href="#">Đà lạt</a>
                <a href="#">Video</a>
                <a href="#">Marketplace</a>
                <a href="#">Băng Feed</a>
                <a href="#">Xem thêm</a>
            </div>

            <div class="col-9 content">

                {{--Creat post--}}
                <div class="create-post">
                    <a href="{{ route('post.create') }}" class="create-button">New post</a>
                </div>
                <!-- Stories Section -->
                <section class="stories-section">
                    <div class="reel-container">
                        <div class="reel-wrapper">
                            <!-- Nút "Tạo tin" CHƯA CÓ HÌNH -->
                            <div class="reel-item create-reel">
                                <a href="{{ route('reel.create') }}" class="create-reel-link">
                                    <div class="story-avatar-container">
                                        <img class="story-avatar" src="{{ auth()->user()->avatar }}"
                                             alt="Your Story" onerror="this.src=''">
                                    </div>
                                    <span class="story-name">Tạo tin</span>
                                    <div class="story-content story-create-content">
                                        <span class="plus-icon">+</span>
                                    </div>
                                </a>
                            </div>

                            @forelse ($reels as $reel)
                                <div class="reel-item">
                                    <div class="story-avatar-container">
                                        <img class="story-avatar" src="{{ $reel->user->avatar }}"
                                             alt="{{ $reel->user->first_name }} {{ $reel->user->last_name }}"
                                             onerror="this.src=''">
                                    </div>
                                    <span class="story-name">{{ $reel->user->first_name }}</span>
                                    <img class="story-content" src="{{ Storage::url($reel->media_url) }}"
                                         alt="Story" onerror="this.src='default-placeholder.jpg'">
                                </div>
                            @empty
                            @endforelse
                        </div>
                        <button class="reel-btn prev" onclick="scrollReel(-1)">&#8249;</button>
                        <button class="reel-btn next" onclick="scrollReel(1)">&#8250;</button>
                    </div>
                </section>

                <!-- Post Section -->
                <section class="post-section">
                    <div class="feed">
                        @forelse ($posts as $post)
                                <div class="post">
                                    <!-- Header: User info and timestamp -->
                                    <div class="post-header">
                                        <div class="user-info">
                                            <img class="post-avatar" src="{{ $post->user->avatar }}" alt="{{ $post->user->name }}">
                                            <div class="user-details">
                                                <h4 class="user-name">{{ $post->user->first_name }} {{ $post->user->last_name }}</h4>
                                                <a href="{{ route('post.show', ['id' => $post->id]) }}"><span class="post-time">{{ $post->created_at->diffForHumans() }}</span></a>
                                            </div>
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
                                                            <button type="submit">
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
                                                @if (isset($item->image_url))  {{-- Nếu là hình ảnh --}}
                                                <img class="post-image" src="{{ $item->image_url }}" alt="Post Image">
                                                @elseif (isset($item->video_url))  {{-- Nếu là video --}}
                                                <video class="post-video" controls>
                                                    <source src="{{ $item->video_url }}" type="video/mp4">
                                                    Your browser does not support the video tag.
                                                </video>
                                                @endif
                                            @endforeach
                                        </div>
                                    @endif


                                    <!-- Post interactions: Like count and comment count -->
                                    <div class="post-stats">
                                         <div class="like-count">
                                            <i class="fas fa-heart"></i>
                                            <span>{{ $post->likes_count ?? 0 }}</span>
                                        </div>
                                        <div class="comment-count">
                                            <span>{{ $post->comments_count ?? 0 }} bình luận</span>
                                        </div>
                                    </div>

                                    <!-- Post actions: Like, Comment, Share -->
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

                                    <!-- Comments section -->
    {{--                                <div class="post-comments">--}}
    {{--                                    @if ($post->comments && count($post->comments) > 0)--}}
    {{--                                        @foreach ($post->comments->take(3) as $comment)--}}
    {{--                                            <div class="comment">--}}
    {{--                                                <img class="comment-avatar" src="{{ $comment->user->avatar }}" alt="{{ $comment->user->name }}">--}}
    {{--                                                <div class="comment-content">--}}
    {{--                                                    <div class="comment-user">{{ $comment->user->name }}</div>--}}
    {{--                                                    <div class="comment-text">{{ $comment->content }}</div>--}}
    {{--                                                    <div class="comment-actions">--}}
    {{--                                                        <span class="comment-time">{{ $comment->created_at->diffForHumans() }}</span>--}}
    {{--                                                        <button class="comment-like-btn">Thích</button>--}}
    {{--                                                        <button class="comment-reply-btn">Trả lời</button>--}}
    {{--                                                    </div>--}}
    {{--                                                </div>--}}
    {{--                                            </div>--}}
    {{--                                        @endforeach--}}

    {{--                                        @if (count($post->comments) > 3)--}}
    {{--                                            <button class="view-more-comments" data-post-id="{{ $post->id }}">--}}
    {{--                                                Xem thêm bình luận--}}
    {{--                                            </button>--}}
    {{--                                        @endif--}}
    {{--                                    @endif--}}

    {{--                                    <!-- Comment form -->--}}
    {{--                                    <div class="comment-form">--}}
    {{--                                        <img class="comment-form-avatar" src="{{ auth()->user()->avatar }}" alt="{{ auth()->user()->name }}">--}}
    {{--                                        <form action="{{ route('comments.store') }}" method="POST" class="comment-input-container">--}}
    {{--                                            @csrf--}}
    {{--                                            <input type="hidden" name="post_id" value="{{ $post->id }}">--}}
    {{--                                            <input type="text" name="content" class="comment-input" placeholder="Viết bình luận...">--}}
    {{--                                            <button type="submit" class="comment-submit">--}}
    {{--                                                <i class="fas fa-paper-plane"></i>--}}
    {{--                                            </button>--}}
    {{--                                        </form>--}}
    {{--                                    </div>--}}
    {{--                                </div>--}}
                                </div>
                        @empty
                            <div class="no-posts">
                                <i class="fas fa-newspaper"></i>
                                <p>Chưa có bài đăng nào.</p>
                                <p class="no-posts-subtext">Hãy theo dõi thêm người dùng để xem bài đăng của họ.</p>
                            </div>
                        @endforelse
                    </div>
                </section>
            </div>
            <!-- Contacts Section -->
            <div class="col-3 friends-list">
                <h3>Danh sách bạn bè</h3>
                <ul>
                    @foreach($friends as $friend)
                        <li>
                            <div class="friend-info">
                                <img src="{{ $friend->avatar ?? asset('images/default-avatar.png') }}" alt="Avatar" class="friend-avatar">
                                <span>{{ $friend->first_name }} {{ $friend->last_name }}</span>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>
    </div>
@endsection

