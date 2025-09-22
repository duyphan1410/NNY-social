@extends('layouts.app')

@section('title', 'Trang chủ')

@push('styles')
    @vite(['resources/css/profile.css'])
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
        <div class="container bg-light-yellow {{ $posts->isEmpty() ? 'home-empty' : '' }}">

            <div class="col-3 sidebar">
                <!-- Hồ sơ cá nhân -->
                <a href="{{ route('profile.me') }}">
                    {{ Auth::user()->first_name }} {{ Auth::user()->last_name }}
                </a>

                <!-- Bạn bè -->
                <a href="{{ route('friend.search') }}">Tìm bạn bè</a>
                <a href="{{ route('friend.index') }}">Bạn bè</a>

                <!-- Nội dung cá nhân -->
                <a href="{{ route('profile.photos', Auth::user()->id) }}">Ảnh</a>
                <a href="{{ route('profile.videos', Auth::user()->id) }}">Video</a>
                <a href="{{ route('profile.me', Auth::user()->id) }}">Bài viết của tôi</a>


                <!-- Cài đặt -->
                <a href="{{ route('account.settings') }} ">Cài đặt tài khoản</a>
                <a href="{{ route('logout') }}"
                   onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    Đăng xuất
                </a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                    @csrf
                </form>
            </div>


            <div class="col-9 content">

                {{--Creat post--}}
                <div class="quick-post-card">
                    <div class="quick-post-header">
                        <img src="{{ auth()->user()->avatar ?? asset('images/default-avatar.png') }}" alt="Avatar" class="quick-post-avatar">
                        <a href="{{ route('post.create') }}" class="quick-post-input">
                            Bạn đang nghĩ gì, {{ auth()->user()->first_name }}?
                        </a>
                    </div>
                    <div class="quick-post-actions">
                        <a href="{{ route('post.create') }}" class="quick-post-action-btn">
                            <i class="bi bi-image"></i>
                            <span>Ảnh</span>
                        </a>
                        <a href="{{ route('post.create') }}" class="quick-post-action-btn">
                            <i class="bi bi-link-45deg"></i>
                            <span>Liên kết</span>
                        </a>
                        <a href="{{ route('post.create') }}" class="quick-post-action-btn primary">
                            <i class="bi bi-pencil-square"></i>
                            <span>Tạo bài viết</span>
                        </a>
                    </div>
                </div>
                <!-- Reel Section -->
{{--                <section class="stories-section">--}}
{{--                    <div class="reel-container">--}}
{{--                        <div class="reel-wrapper">--}}
{{--                            <!-- Nút "Tạo tin" CHƯA CÓ HÌNH -->--}}
{{--                            <div class="reel-item create-reel">--}}
{{--                                <a href="{{ route('reel.create') }}" class="create-reel-link">--}}
{{--                                    <div class="story-avatar-container">--}}
{{--                                        <img class="story-avatar" src="{{ auth()->user()->avatar }}"--}}
{{--                                             alt="Your Story" onerror="this.src=''">--}}
{{--                                    </div>--}}
{{--                                    <span class="story-name">Tạo tin</span>--}}
{{--                                    <div class="story-content story-create-content">--}}
{{--                                        <span class="plus-icon">+</span>--}}
{{--                                    </div>--}}
{{--                                </a>--}}
{{--                            </div>--}}

{{--                            @forelse ($reels as $reel)--}}
{{--                                <div class="reel-item">--}}
{{--                                    <div class="story-avatar-container">--}}
{{--                                        <img class="story-avatar" src="{{ $reel->user->avatar }}"--}}
{{--                                             alt="{{ $reel->user->first_name }} {{ $reel->user->last_name }}"--}}
{{--                                             onerror="this.src=''">--}}
{{--                                    </div>--}}
{{--                                    <span class="story-name">{{ $reel->user->first_name }}</span>--}}
{{--                                    <img class="story-content" src="{{ Storage::url($reel->media_url) }}"--}}
{{--                                         alt="Story" onerror="this.src='default-placeholder.jpg'">--}}
{{--                                </div>--}}
{{--                            @empty--}}
{{--                            @endforelse--}}
{{--                        </div>--}}
{{--                        <button class="reel-btn prev" onclick="scrollReel(-1)">&#8249;</button>--}}
{{--                        <button class="reel-btn next" onclick="scrollReel(1)">&#8250;</button>--}}
{{--                    </div>--}}
{{--                </section>--}}

                <!-- Post Section -->
                <section class="post-section">
                    <div class="feed">
                        @forelse ($posts as $post)
                            @include('post.card', ['post' => $post])
                        @empty
                            <div class="empty-feed bg-white shadow rounded-lg p-6 text-center">
                                <i class="fas fa-newspaper text-gray-300 text-5xl mb-3"></i>
                                <p class="text-gray-600">Chưa có bài viết nào</p>
                                <p class="text-gray-500 text-sm mt-2">Hãy đăng bài viết đầu tiên của bạn!</p>
                            </div>
                        @endforelse
                    </div>
                </section>
            </div>
            <!-- Contacts Section -->
            <div class="col-3 friends-list">
                <h3>Danh sách bạn bè</h3>
                <ul class="{{ $friends->isEmpty() ? 'no-friends-ul' : '' }}">
                    @forelse ($friends as $friend)
                        <li>
                            <div class="friend-info">
                                <div class="friend-left">
                                    <a href="{{ route('profile.show', $friend->id) }}">
                                        <img src="{{ $friend->avatar ?? asset('images/default-avatar.png') }}" alt="Avatar" class="friend-avatar">
                                        <span class="friend-name">{{ $friend->first_name }} {{ $friend->last_name }}</span>
                                    </a>
                                </div>
                                <button class="open-chat-btn"
                                        data-user-id="{{ $friend->id }}"
                                        data-user-name="{{ $friend->first_name }} {{ $friend->last_name }}">
                                    <i class="fa fa-comment"></i>
                                </button>
                            </div>
                        </li>
                    @empty
                        <li class="no-friends-message">
                            <i class="fas fa-user-friends no-friends-icon"></i>
                            <p>Chưa có bạn bè nào.</p>
                            <p class="no-friends-action">
                                <a href="{{ route('friend.search') }}" >Tìm bạn bè</a> ngay bây giờ!
                            </p>
                        </li>
                    @endforelse
                </ul>
            </div>
    </div>
@endsection

