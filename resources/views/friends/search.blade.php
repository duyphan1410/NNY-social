@extends('layouts.app')

@section('content')
    <h2>Tìm kiếm bạn bè</h2>
    @if(session('success'))
        <p style="color: green;">{{ session('success') }}</p>
    @endif

    @if(session('error'))
        <p style="color: red;">{{ session('error') }}</p>
    @endif

    <form action="{{ route('friend.search') }}" method="GET">
        <input type="text" name="query" placeholder="Nhập thông tin muốn tìm... " required>
        <button type="submit">Tìm kiếm</button>
    </form>

    @if(isset($users) && count($users) > 0)
        <ul>
            @foreach ($users as $user)
                <div>
                    <p>{{ $user->first_name }} {{ $user->last_name }}</p>

                    @if(in_array($user->id, $pendingIds))
                        <form action="{{ route('friend.cancelRequest') }}" method="POST">
                            @csrf
                            <input type="hidden" name="receiver_id" value="{{ $user->id }}">
                            <button type="submit">Hủy lời mời</button>
                        </form>
                    @elseif(in_array($user->id, $rejectedIds))
                        <button disabled>Bạn đã gửi lời mời trước đó hoặc bị từ chối</button>
                    @else
                        <form action="{{ route('friend.request') }}" method="POST">
                            @csrf
                            <input type="hidden" name="receiver_id" value="{{ $user->id }}">
                            <button type="submit">Gửi lời mời</button>
                        </form>
                    @endif
                </div>
            @endforeach

        </ul>
    @elseif(isset($users))
        <p>Không tìm thấy người dùng nào.</p>
    @endif

    <h2>Lời mời kết bạn đã gửi</h2>

    @if($pendingRequests->isNotEmpty())
        <ul>
            @foreach($pendingRequests as $request)
                <li>
                    {{ $request->receiver->first_name }} {{ $request->receiver->last_name }}
                    <form action="{{ route('friend.cancelRequest') }}" method="POST" style="display: inline;">
                        @csrf
                        <input type="hidden" name="receiver_id" value="{{ $request->receiver_id }}">
                        <button type="submit">Hủy lời mời</button>
                    </form>
                </li>
            @endforeach
        </ul>
    @else
        <p>Bạn chưa gửi lời mời nào.</p>
    @endif
@endsection
