@extends('layouts.app')

@push('styles')
    @vite(['resources/css/admin.css'])
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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

        <!-- Main Content -->
        <div class="admin-content">
            <div class="admin-content-header">
                <h2 class="admin-content-title">Thống kê</h2>
            </div>

            <div class="admin-grid">
                <div class="admin-card">
                    <h4 class="admin-card-title">Người dùng mới (6 tháng qua)</h4>
                    <canvas id="newUsersChart"></canvas>
                </div>

                <div class="admin-card">
                    <h4 class="admin-card-title">Bài đăng theo tháng</h4>
                    <canvas id="postsChart"></canvas>
                </div>

                <div class="admin-card">
                    <h4 class="admin-card-title">Tổng người dùng bị khóa</h4>
                    <div class="admin-stat-count danger">{{ $bannedUsers }}</div>
                </div>

                <div class="admin-card">
                    <h4 class="admin-card-title">Tương tác trong 30 ngày</h4>
                    <div class="admin-stat-item">
                        <span class="admin-stat-label">❤️ Likes:</span>
                        <span class="admin-stat-value">{{ $interactions['likes'] }}</span>
                    </div>
                    <div class="admin-stat-item">
                        <span class="admin-stat-label">💬 Comments:</span>
                        <span class="admin-stat-value">{{ $interactions['comments'] }}</span>
                    </div>
                </div>

                <div class="admin-card admin-card-full-width">
                    <h4 class="admin-card-title">Top 5 người dùng có nhiều bài đăng nhất</h4>
                    <ul class="admin-stat-list">
                        @foreach($topUsers as $user)
                            <li>
                                <span>{{ $user->first_name }} {{ $user->last_name }}</span>
                                <span class="admin-stat-value">{{ $user->posts_count }} bài đăng</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Kiểm tra xem các element có tồn tại không
            const newUsersCanvas = document.getElementById('newUsersChart');
            const postsCanvas = document.getElementById('postsChart');

            if (newUsersCanvas && postsCanvas) {
                const newUsersCtx = newUsersCanvas.getContext('2d');
                const postsCtx = postsCanvas.getContext('2d');

                // Chart người dùng mới
                new Chart(newUsersCtx, {
                    type: 'bar',
                    data: {
                        labels: {!! json_encode($newUsersMonthly->pluck('month')) !!},
                        datasets: [{
                            label: 'Người dùng mới',
                            data: {!! json_encode($newUsersMonthly->pluck('count')) !!},
                            backgroundColor: 'rgba(54, 162, 235, 0.6)',
                            borderColor: 'rgba(54, 162, 235, 1)',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    precision: 0
                                }
                            }
                        }
                    }
                });

                // Chart bài đăng theo tháng
                new Chart(postsCtx, {
                    type: 'line',
                    data: {
                        labels: {!! json_encode($postsMonthly->pluck('month')) !!},
                        datasets: [{
                            label: 'Bài đăng',
                            data: {!! json_encode($postsMonthly->pluck('count')) !!},
                            backgroundColor: 'rgba(255, 99, 132, 0.2)',
                            borderColor: 'rgba(255, 99, 132, 1)',
                            borderWidth: 2,
                            fill: true,
                            tension: 0.1
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    precision: 0
                                }
                            }
                        }
                    }
                });

                console.log('Charts initialized successfully');
            } else {
                console.error('Chart canvas elements not found');
            }
        });
    </script>
@endpush
