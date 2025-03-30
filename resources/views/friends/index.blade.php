@extends('layouts.app')

@push('styles')
    @vite(['resources/css/friends.css'])
@endpush

@section('content')
    <div class="friends-container">
        <!-- Danh sách bạn bè -->
        <div class="friends-list">
            <h2>Danh sách bạn bè</h2>

            @if($friends->isEmpty())
                <p class="empty-text">Bạn chưa có bạn bè nào.</p>
            @else
                <ul>
                    @foreach($friends as $friend)
                        <li>
                            <div class="friend-info">
                                <img src="{{ $friend->avatar ?? asset('images/default-avatar.png') }}" alt="Avatar" class="friend-avatar">
                                <span>{{ $friend->first_name }} {{ $friend->last_name }}</span>
                            </div>
                            <form action="{{ route('friend.unfriend') }}" method="POST">
                                @csrf
                                <input type="hidden" name="friend_id" value="{{ $friend->id }}">
                                <button type="submit" class="friend-btn unfriend-btn">Hủy kết bạn</button>
                            </form>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>

        <!-- Lời mời kết bạn -->
        <div class="friend-requests">
            <h2>Lời mời kết bạn</h2>

            @if(session('success'))
                <p class="alert alert-success">{{ session('success') }}</p>
            @endif

            @if(session('error'))
                <p class="alert alert-error">{{ session('error') }}</p>
            @endif

            @if($pendingRequests->isEmpty())
                <p class="empty-text">Không có lời mời kết bạn nào.</p>
            @else
                <ul>
                    @foreach($pendingRequests as $request)
                        <li>
                            <div class="friend-info">
                                <img src="{{ $request->sender->avatar ?? asset('images/default-avatar.png') }}" alt="Avatar" class="friend-avatar">
                                <span>{{ $request->sender->first_name }} {{ $request->sender->last_name }}</span>
                            </div>
                            <div class="friend-actions">
                                <form action="{{ route('friend.accept') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="request_id" value="{{ $request->id }}">
                                    <button type="submit" class="friend-btn accept-btn">Chấp nhận</button>
                                </form>

                                <form action="{{ route('friend.reject') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="request_id" value="{{ $request->id }}">
                                    <button type="submit" class="friend-btn reject-btn">Từ chối</button>
                                </form>
                            </div>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>
@endsection
