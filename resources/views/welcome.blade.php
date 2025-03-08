@extends('layouts.home')

@section('content')
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Page wellcome</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />

        <!-- Styles -->

    </head>
    <body class="antialiased">
    <section class="post-section">
        <div class="feed">
            @forelse ($posts as $post)
                <div class="post">
                    <!-- Avatar người dùng -->
                    <img class="post-avatar" src="{{ $post->user->avatar_url ?? 'default-avatar.jpg' }}" alt="{{ $post->user->name }}">
                    <!-- Nội dung bài đăng -->
                    <p class="post-text">{{ $post->content }}</p>
                    <!-- Hình ảnh bài đăng -->
                    @if ($post->images)
                        @foreach ($post->images as $image)
                            <img class="post-image" src="{{ asset('storage/' . $image->image_url) }}" alt="Post Image">
                        @endforeach
                    @endif
                </div>
            @empty
                <p class="no-posts">Chưa có bài đăng nào.</p>
            @endforelse
        </div>
    </section>
    </body>

@endsection
