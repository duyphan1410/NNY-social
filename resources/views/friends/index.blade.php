@extends('layouts.app')

@section('content')
    <h2>Danh sách bạn bè</h2>
    <ul>
        @foreach($friends as $friend)
            <li>
                {{ $friend->user_id == Auth::id() ? $friend->friend->full_name : $friend->user->full_name }}
            </li>
        @endforeach
    </ul>

    <h2>Lời mời kết bạn đang chờ</h2>
    @if(session('success'))
        <p style="color: green;">{{ session('success') }}</p>
    @endif

    @if(session('error'))
        <p style="color: red;">{{ session('error') }}</p>
    @endif    <ul>
        @foreach($pendingRequests as $request)
            <li>
                {{ $request->sender->first_name }} {{ $request->sender->last_name }}
                <form action="{{ route('friend.accept') }}" method="POST" style="display: inline;">
                    @csrf
                    <input type="hidden" name="request_id" value="{{ $request->id }}">
                    <button type="submit">Chấp nhận</button>
                </form>

                <form action="{{ route('friend.reject') }}" method="POST" style="display: inline;">
                    @csrf
                    <input type="hidden" name="request_id" value="{{ $request->id }}">
                    <button type="submit">Từ chối</button>
                </form>
            </li>
        @endforeach
    </ul>
@endsection
