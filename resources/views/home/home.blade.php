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
        <div class="container bg-light-yellow">

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
                            @include('post.card', ['post' => $post])
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

