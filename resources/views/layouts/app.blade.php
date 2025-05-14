<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <meta name="user-id" content="{{ auth()->id() }}">

    <title>{{ config('app.name', 'NNY') }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Cho phép trang con thêm CSS/JS riêng --}}
    @stack('styles')
    @stack('scripts')
</head>
<body>
<div id="app">
    <nav class="navbar">
        <div class="ctn">
            <a class="navbar-brand" href="{{ url('/home') }}">
                <svg viewBox="0 0 24 24" fill="currentColor">
                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8z"/>
                </svg>
                <img  height="24" src="{{ asset('/img/logo.png') }}" alt="Logo">
            </a>
            <div class="nav-links">
                @guest
                    @if (Route::has('login'))
                        <a class="nav-link" href="{{ route('login') }}">
                            <i class="fa fa-sign-in-alt"></i> {{ __('Đăng nhập') }}
                        </a>
                    @endif

                    @if (Route::has('register'))
                        <a class="nav-link" href="{{ route('register') }}">
                            <i class="fa fa-user-plus"></i> {{ __('Đăng ký') }}
                        </a>
                    @endif
                @else
                    <a href="{{ route('profile.me') }}" class="font-semibold text-blue-600 user-profile-link">
                        <span class="nav-link">Chào, {{ Auth::user()->first_name }} {{ Auth::user()->last_name }}</span>
                    </a>
                    <div class="nav-link">
                        <button id="notification-btn" class="nav-link">
                            <i class="fa fa-bell"></i>
                            <span id="notification-count" class="absolute top-0 right-0 bg-red-500 text-white text-xs rounded-full px-1 hidden">0</span>
                        </button>
                        <div id="notification-dropdown" class="hidden absolute right-0 mt-2 w-80 bg-white shadow-lg rounded-lg z-50">
                            <ul id="notification-list">
                                @forelse(auth()->user()->notifications()->orderBy('created_at', 'desc')->take(5)->get() as $notification)
                                    <li class="notification-item {{ is_null($notification->read_at) ? 'unread' : '' }}" data-id="{{ $notification->id }}">
                                        <a href="{{ $notification->url }}">
                                            <div class="notification-message">{{ $notification->message }}</div>
                                            <div class="notification-time">{{ $notification->created_at->diffForHumans() }}</div>
                                        </a>
                                    </li>
                                @empty
                                    <li class="text-center p-2 text-gray-400">Không có thông báo nào</li>
                                @endforelse
                            </ul>
                            <button id="mark-all-read" class="w-full text-blue-600 hover:underline p-2">Đánh dấu tất cả đã đọc</button>
                        </div>
                    </div>

                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                        @csrf
                    </form>
                    <a class="nav-link" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <i class="fa fa-sign-out-alt"></i>
                    </a>
                @endguest
            </div>
        </div>
    </nav>

    <main>
        @yield('content')
    </main>
</div>
</body>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    const btn = document.getElementById('notification-btn');
    const dropdown = document.getElementById('notification-dropdown');

    btn.addEventListener('click', (e) => {
        e.stopPropagation();
        dropdown.style.display = dropdown.style.display === 'block' ? 'none' : 'block';
    });

    document.addEventListener('click', (e) => {
        if (!dropdown.contains(e.target)) {
            dropdown.style.display = 'none';
        }
    });
</script>

</html>
