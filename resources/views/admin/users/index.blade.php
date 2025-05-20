@extends('layouts.app')

@push('styles')
    @vite(['resources/css/admin.css'])
@endpush


@section('content')
    <div class="admin-container">
        <!-- Sidebar -->
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
                        <a href="{{ route('admin.posts') }}" class="admin-nav-link">
                            <i class="bi bi-file-earmark-text"></i>
                            <span class="admin-nav-text">Quản lý bài đăng</span>


                        </a>
                    </li>
                    <li class="admin-nav-item">
                        <a href="{{ route('admin.statistics') }}" class="admin-nav-link">
                            <i class="bi bi-bar-chart-line"></i>
                            <span class="admin-nav-text">Thống kê</span>

                        </a>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Main Content -->
        <div class="admin-content">
            <div class="admin-content-header">
                <h2 class="admin-content-title">Quản lý người dùng</h2>
            </div>

            <form method="GET" class="admin-search-form">
                <input type="text" name="search" placeholder="Tìm tên hoặc username..." value="{{ request('search') }}" class="admin-search-input">
                <button type="submit" class="admin-search-button">
                    <i class="bi bi-search"></i> Tìm
                </button>
            </form>

            @if($users->count() > 0)
                <div class="table-responsive">
                    <table class="admin-table">
                        <thead>
                        <tr>
                            <th>Tên</th>
                            <th>Username</th>
                            <th>Email</th>
                            <th>Trạng thái</th>
                            <th>Hành động</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($users as $user)
                            <tr>
                                <td>{{ $user->first_name }} {{ $user->last_name }}</td>
                                <td>{{ $user->username }}</td>
                                <td>{{ $user->email }}</td>
                                <td>
                                    @if($user->banned)
                                        <span class="status-badge status-banned">Tài khoản bị khóa</span>
                                    @else
                                        <span class="status-badge status-active">Hoạt động</span>
                                    @endif
                                </td>
                                <td>
                                    @if(!$user->is_admin)
                                        <form method="POST" action="{{ route('admin.users.toggleBan', $user->id) }}">
                                            @csrf
                                            <button type="submit" class="admin-btn admin-btn-sm {{ $user->banned ? 'admin-btn-success' : 'admin-btn-warning' }}">
                                                {{ $user->banned ? 'Mở khóa' : 'Khóa' }}
                                            </button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="alert alert-info">
                    <i class="bi bi-info-circle"></i> Không tìm thấy người dùng nào.
                </div>
            @endif

            <div class="admin-pagination text-sm [&>nav>div>span]:px-2 [&>nav>div>span]:py-1 [&>nav>div>span]:text-sm [&>nav>div>span>svg]:w-4 [&>nav>div>span>svg]:h-4">
                {{ $users->links() }}
            </div>
        </div>
    </div>
@endsection
