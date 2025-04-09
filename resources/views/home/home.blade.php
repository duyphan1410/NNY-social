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
                <a href="{{ route('profile.me') }}">{{ Auth::user()->first_name }} {{ Auth::user()->last_name }}</a>
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
                    <a href="{{ route('post.create') }}" class="create-button">Tạo bài đăng</a>
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
                            @php
                                $isOwner = auth()->check() && auth()->id() === $post->user->id;
                                $profileRoute = $isOwner
                                    ? route('profile.me')
                                    : route('profile.show', $post->user->id);
                            @endphp
                            <div class="post">
                                <div class="post-header">
                                    <div class="user-info">
                                        <a href="{{$profileRoute}}">
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
                                    <a href="{{ route('post.show', ['id' => $post->id]) }}"
                                       class="action-btn comment-btn">
                                        <i class="fas fa-comment"></i>
                                        <span>Bình luận</span>
                                    </a>
                                    <a href="{{ route('post.share.form', ['id' => $post->id]) }}" class="action-btn share-btn">
                                        <i class="fas fa-share"></i>
                                        <span>Chia sẻ</span>
                                    </a>
                                </div>

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
                                <a href="{{ route('profile.show', $friend->id) }}">
                                    <img src="{{ $friend->avatar ?? asset('images/default-avatar.png') }}" alt="Avatar" class="friend-avatar">
                                    <span class="friend-name">{{ $friend->first_name }} {{ $friend->last_name }}</span>
                                </a>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>
    </div>
@endsection

