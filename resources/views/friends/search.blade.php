@extends('layouts.app')

@push('styles')
    @vite(['resources/css/friends.css'])
@endpush

@section('content')
    <div class="friends-container">
        <!-- Cột kết quả tìm kiếm -->
        <div class="friends-list">
            <h2>Tìm kiếm bạn bè</h2>
            @if(session('success'))
                <p class="alert alert-success">{{ session('success') }}</p>
            @endif
            @if(session('error'))
                <p class="alert alert-error">{{ session('error') }}</p>
            @endif
            <form action="{{ route('friend.search') }}" method="GET" class="search-box">
                <input type="text" name="query" placeholder="Nhập thông tin muốn tìm... " required>
                <button type="submit">Tìm kiếm</button>
            </form>
            @if(isset($users) && count($users) > 0)
                <ul>
                    @foreach ($users as $user)
                        <li>
                            <div class="friend-info">
                                <img src="{{ $user->avatar ?? asset('images/default-avatar.png') }}" alt="Avatar" class="friend-avatar">
                                <span>{{ $user->first_name }} {{ $user->last_name }}</span>
                            </div>
                            <div>
                                @if(in_array($user->id, $pendingIds))
                                    <form action="{{ route('friend.cancelRequest') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="receiver_id" value="{{ $user->id }}">
                                        <button class="friend-btn reject-btn" type="submit">Hủy lời mời</button>
                                    </form>
                                @elseif(in_array($user->id, $rejectedIds))
                                    <button class="friend-btn" disabled>Bạn đã gửi lời mời trước đó hoặc bị từ chối</button>
                                @else
                                    <form action="{{ route('friend.request') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="receiver_id" value="{{ $user->id }}">
                                        <button class="friend-btn accept-btn" type="submit">Gửi lời mời</button>
                                    </form>
                                @endif
                            </div>
                        </li>
                    @endforeach
                </ul>
            @elseif(isset($users))
                <p class="empty-text">Không tìm thấy người dùng nào.</p>
            @endif
        </div>

        <!-- Cột lời mời đã gửi -->
        <div class="friend-requests">
            <h2>Lời mời kết bạn đã gửi</h2>
            @if($pendingRequests->isNotEmpty())
                <ul>
                    @foreach($pendingRequests as $request)
                        <li>
                            <div class="friend-info">
                                <img src="{{ $request->receiver->avatar ?? asset('images/default-avatar.png') }}" alt="Avatar" class="friend-avatar">
                                <span>{{ $request->receiver->first_name }} {{ $request->receiver->last_name }}</span>
                            </div>
                            <form action="{{ route('friend.cancelRequest') }}" method="POST">
                                @csrf
                                <input type="hidden" name="receiver_id" value="{{ $request->receiver_id }}">
                                <button class="friend-btn reject-btn" type="submit">Hủy lời mời</button>
                            </form>
                        </li>
                    @endforeach
                </ul>
            @else
                <p class="empty-text">Bạn chưa gửi lời mời nào.</p>
            @endif
        </div>
    </div>
@endsection
