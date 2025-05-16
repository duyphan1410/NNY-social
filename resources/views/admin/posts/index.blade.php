@extends('layouts.app')

@push('styles')
    @vite(['resources/css/admin.css'])
@endpush

@section('content')
    <div class="admin-container">
        <div class="admin-sidebar">
            <div class="sidebar-container">
                <div class="sidebar-header">
                    <h5 class="sidebar-title">Bảng điều khiển</h5>
                </div>

                <ul class="admin-nav">
                    <li class="admin-nav-item">
                        <a href="{{ route('admin.dashboard') }}" class="admin-nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                            <i class="bi bi-person-fill"></i>
                            <span class="admin-nav-text">Quản lý người dùng</span>
                        </a>
                    </li>
                    <li class="admin-nav-item">
                        <a href="{{ route('admin.posts') }}" class="admin-nav-link {{ request()->routeIs('admin.posts') ? 'active' : '' }}">
                            <i class="bi bi-file-earmark-text"></i>
                            <span class="admin-nav-text">Quản lý bài đăng</span>
                        </a>
                    </li>
                    <li class="admin-nav-item">
                        <a href="{{ route('admin.statistics') }}" class="admin-nav-link {{ request()->routeIs('admin.statistics') ? 'active' : '' }}">
                            <i class="bi bi-bar-chart-line"></i>
                            <span class="admin-nav-text">Thống kê</span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        <div class="admin-content">
            <div class="admin-content-header">
                <h2 class="admin-content-title">Quản lý bài đăng</h2>
            </div>

            <form method="GET" class="admin-search-form mb-4">
                <input type="text" name="search" placeholder="Tìm kiếm bài đăng..." value="{{ request('search') }}" class="admin-search-input">
                <button type="submit" class="admin-search-button">
                    <i class="bi bi-search"></i> Tìm
                </button>
            </form>

            @if($posts->count())
                <div class="table-responsive">
                    <table class="admin-table">
                        <thead>
                        <tr>
                            <th>Tác giả</th>
                            <th>Nội dung</th>
                            <th>Ngày đăng</th>
                            <th>Loại</th>
                            <th>Hành động</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($posts as $post)
                            <tr>
                                <td>{{ $post->user->first_name }} {{ $post->user->last_name }}</td>
                                <td>{{ Str::limit(strip_tags($post->content), 30) }}</td>
                                <td>{{ $post->created_at->format('d/m/Y H:i') }}</td>
                                <td>
                                    @if($post->shared_post_id)
                                        <span class="status-badge status-shared">Bài chia sẻ</span>
                                    @else
                                        <span class="status-badge status-original">Bài gốc</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="admin-action-buttons">
                                        <a href="{{ route('post.show', $post->id) }}" class="admin-btn admin-btn-sm admin-btn-view" target="_blank">
                                            <i class="bi bi-eye"></i> Xem
                                        </a>

                                        @if($post->user->id !== auth()->id() && !$post->user->is_admin)
                                            <form method="POST" action="{{ route('admin.posts.toggleVisibility', $post->id) }}" class="d-inline">
                                                @csrf
                                                <button type="submit" class="admin-btn admin-btn-sm {{ $post->is_hidden ? 'admin-btn-success' : 'admin-btn-warning' }}">
                                                    @if($post->is_hidden)
                                                        <i class="bi bi-eye"></i> Hiện
                                                    @else
                                                        <i class="bi bi-eye-slash"></i> Ẩn
                                                    @endif
                                                </button>
                                            </form>
                                        @endif

                                        <form action="{{ route('admin.posts.destroy', $post->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc chắn muốn xóa bài đăng này?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="admin-btn admin-btn-sm admin-btn-danger">
                                                <i class="bi bi-trash"></i> Xóa
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="alert alert-info">
                    <i class="bi bi-info-circle"></i> Chưa có bài đăng nào.
                </div>
            @endif

            <div class="admin-pagination">
                {{ $posts->links() }}
            </div>
        </div>
    </div>
@endsection
