@extends('layouts.app')

@push('styles')
    @vite(['resources/css/notification.css'])
@endpush

@push('scripts')
    @vite(['resources/js/notification.js'])
@endpush

@section('content')
    <div class="nf-container">
        <div class="nf-header">
            <h2 class="nf-title">
                <svg xmlns="http://www.w3.org/2000/svg" class="nf-title-icon" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
                    <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
                </svg>
                Thông báo
            </h2>
        </div>

        <div class="nf-filter">
            <a href="{{ route('notifications.index') }}"
               class="nf-btn nf-btn-filter {{ request()->routeIs('notifications.index') && !request()->has('filter') ? 'active' : '' }}">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="8" y1="6" x2="21" y2="6"></line>
                    <line x1="8" y1="12" x2="21" y2="12"></line>
                    <line x1="8" y1="18" x2="21" y2="18"></line>
                    <line x1="3" y1="6" x2="3.01" y2="6"></line>
                    <line x1="3" y1="12" x2="3.01" y2="12"></line>
                    <line x1="3" y1="18" x2="3.01" y2="18"></line>
                </svg>
                Tất cả
            </a>
            <a href="{{ route('notifications.index', ['filter' => 'unread']) }}"
               class="nf-btn nf-btn-filter {{ request()->has('filter') && request('filter') == 'unread' ? 'active' : '' }}">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"></circle>
                    <line x1="12" y1="16" x2="12" y2="12"></line>
                    <line x1="12" y1="8" x2="12.01" y2="8"></line>
                </svg>
                Chưa đọc
            </a>
            <button id="mark-all-read-btn" class="nf-btn nf-btn-mark-all">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                    <polyline points="22 4 12 14.01 9 11.01"></polyline>
                </svg>
                Đánh dấu tất cả đã đọc
            </button>
        </div>

        <ul id="notification-list" class="nf-list">
            @forelse($notifications as $notification)
                <li class="nf-item {{ is_null($notification->read_at) ? 'nf-unread' : '' }}">
                    <div class="nf-content">
                        <a href="{{ $notification->url }}" class="nf-message">
                            {{ $notification->message }}
                        </a>
                        <div class="nf-time">
                            <svg xmlns="http://www.w3.org/2000/svg" class="nf-time-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="10"></circle>
                                <polyline points="12 6 12 12 16 14"></polyline>
                            </svg>
                            {{ $notification->created_at->diffForHumans() }}
                        </div>
                    </div>
                    <div class="nf-actions">
                        <form method="POST" action="{{ route('notifications.destroy', ['id' => $notification->id]) }}">
                            @csrf
                            @method('DELETE')
                            <button onclick="return confirm('Bạn có chắc chắn muốn xóa thông báo này?')" class="nf-btn-delete">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="3 6 5 6 21 6"></polyline>
                                    <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                    <line x1="10" y1="11" x2="10" y2="17"></line>
                                    <line x1="14" y1="11" x2="14" y2="17"></line>
                                </svg>
                                Xóa
                            </button>
                        </form>
                    </div>
                </li>
            @empty
                <li class="nf-empty">
                    <svg xmlns="http://www.w3.org/2000/svg" class="nf-empty-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                    </svg>
                    <p class="nf-empty-text">Không có thông báo nào</p>
                </li>
            @endforelse
        </ul>

        <!-- Loading indicator -->
        <div class="nf-loading" id="loading-indicator">
            <div class="nf-loader"></div>
            <p>Đang tải...</p>
        </div>

        <div class="nf-pagination">
            {{ $notifications->links() }}
        </div>

        <!-- Success toast (hidden by default) -->
        <div class="nf-toast nf-toast-success hiding" id="success-toast" style="display: none;">
            <svg xmlns="http://www.w3.org/2000/svg" class="nf-toast-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                <polyline points="22 4 12 14.01 9 11.01"></polyline>
            </svg>
            <span class="nf-toast-message">Thao tác thành công!</span>
        </div>
    </div>
@endsection
