<?php

namespace App\Http\Controllers\Friends;

use App\Http\Controllers\Controller;
use App\Models\Friend;
use App\Models\FriendRequest;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;


class FriendController extends Controller
{

    public function index()
    {
        $friends = Friend::where('user_id', Auth::id())
            ->orWhere('friend_id', Auth::id())
            ->with(['user', 'friend'])
            ->get()
            ->map(function ($friend) {
                return $friend->user_id === Auth::id() ? $friend->friend : $friend->user;
            });

        // Lấy danh sách lời mời kết bạn đang chờ (những người gửi lời mời đến mình)
        $pendingRequests = FriendRequest::where('receiver_id', Auth::id())
            ->where('status', 'pending')
            ->with('sender')
            ->get();

        return view('friends.index', compact('friends','pendingRequests'));
    }


    // Gửi lời mời kết bạn
    public function sendRequest(Request $request)
    {
        $receiverId = $request->input('receiver_id');
        $senderId = Auth::id();

        // Kiểm tra người nhận có tồn tại không
        $receiver = User::find($receiverId);
        if (!$receiver) {
            return redirect()->back()->with('error', 'Người dùng không tồn tại.');
        }

        // Kiểm tra nếu đã là bạn bè
        $isFriend = Friend::where([
            ['user_id', Auth::id()],
            ['friend_id', $receiverId]
        ])->orWhere([
            ['user_id', $receiverId],
            ['friend_id', Auth::id()]
        ])->exists();

        if ($isFriend) {
            return redirect()->back()->with('error', 'Bạn đã là bạn bè.');
        }

        // Kiểm tra xem đã có lời mời nào giữa 2 người chưa
        $existingRequest = FriendRequest::where(function ($query) use ($senderId, $receiverId) {
            $query->where('sender_id', $senderId)->where('receiver_id', $receiverId);
        })->orWhere(function ($query) use ($senderId, $receiverId) {
            $query->where('sender_id', $receiverId)->where('receiver_id', $senderId);
        })->first();

        if ($existingRequest && $existingRequest->receiver_id == $senderId) {
            // Kiểm tra nếu lời mời là từ người nhận đến người gửi
            return redirect()->back()->with('error', 'Người này đã gửi lời mời kết bạn cho bạn. <br> Hãy kiểm tra và chấp nhận.');
        }

        // Gửi lời mời kết bạn
        FriendRequest::create([
            'sender_id' => Auth::id(),
            'receiver_id' => $receiverId,
            'status' => 'pending'
        ]);

        return redirect()->back()->with('success', 'Lời mời đã được gửi.');
    }


    // Chấp nhận lời mời kết bạn
    public function acceptRequest(Request $request)
    {
        $requestId = $request->input('request_id');

        // Kiểm tra xem lời mời có tồn tại hay không
        $friendRequest = FriendRequest::where('id', $requestId)
            ->where('receiver_id', Auth::id())
            ->where('status', 'pending')
            ->first();

        if (!$friendRequest) {
            return redirect()->back()->with('error', 'Lời mời này đã bị hủy.');
        }

        // Thêm bạn bè vào bảng friends
        Friend::create([
            'user_id'   => min(Auth::id(), $friendRequest->sender_id),
            'friend_id' => max(Auth::id(), $friendRequest->sender_id)
        ]);


        // Cập nhật trạng thái thành "accepted"
        $friendRequest->update(['status' => 'accepted']);

        session()->flash('friend_accepted', Auth::user()->first_name . ' ' . Auth::user()->last_name . ' đã chấp nhận lời mời kết bạn của bạn.');

        return redirect()->back()->with('success', 'Bạn đã chấp nhận lời mời.');
    }


    // Từ chối lời mời kết bạn
    public function rejectRequest(Request $request)
    {
        $requestId = $request->input('request_id');

        $friendRequest = FriendRequest::where('id', $requestId)
            ->where('receiver_id', Auth::id())
            ->where('status', 'pending')
            ->first();

        if (!$friendRequest) {
            return response()->json(['message' => 'Không tìm thấy lời mời'], 404);
        }

        $friendRequest->update(['status' => 'rejected']);

        session()->flash('friend_rejected', Auth::user()->first_name . ' ' . Auth::user()->last_name . ' đã từ chối lời mời kết bạn của bạn.');

        return redirect()->back()->with('success', 'Bạn đã từ chối lời mời.');
    }

    // Hủy kết bạn
    public function unfriend(Request $request)
    {
        $friendId = $request->input('friend_id');

        // Kiểm tra dữ liệu đầu vào
        if (!$friendId || !is_numeric($friendId)) {
            return redirect()->back()->with('error', 'ID bạn bè không hợp lệ');
        }

        $userId = Auth::id();

        // Kiểm tra xem có phải bạn bè không trước khi xóa
        $friendship = Friend::whereIn('user_id', [$userId, $friendId])
            ->whereIn('friend_id', [$userId, $friendId])
            ->first();

        if (!$friendship) {
            return redirect()->back()->with('error', 'Không tìm thấy bạn bè');
        }

        // Lấy tên
        $friend = User::find($friendId);
        $friendName = $friend ? $friend->name : 'người dùng này';

        // Xóa quan hệ bạn bè
        $friendship->delete();

        FriendRequest::where(function ($query) use ($userId, $friendId) {
            $query->where('sender_id', $userId)
                ->where('receiver_id', $friendId);
        })->orWhere(function ($query) use ($userId, $friendId) {
            $query->where('sender_id', $friendId)
                ->where('receiver_id', $userId);
        })->delete();

        return redirect()->back()->with('success_unfriend', "Đã hủy kết bạn với {$friendName} thành công");
    }


    // Lấy danh sách bạn bè
    public function listFriends()
    {
        $friends = Friend::where('user_id', Auth::id())->get();

        $friendList = $friends->map(function ($friend) {
            return $friend->friend;
        });

        return response()->json($friendList);
    }

    //Tìm kiếm bạn
    public function search(Request $request)
    {
        $query = $request->input('query');
        $userId = Auth::id();

        // Lấy danh sách lời mời đã gửi (luôn có, ngay cả khi chưa tìm kiếm)

        // Lấy danh sách lời mời đã gửi (để kiểm tra trạng thái)
        $sentRequests = FriendRequest::where('sender_id', $userId)
            ->whereIn('status', ['pending', 'rejected']) // Kiểm tra cả rejected
            ->get();

        // Lưu danh sách ID của người đã được gửi lời mời
        $pendingRequests = $sentRequests->where('status', 'pending');
        $pendingIds = $pendingRequests->pluck('receiver_id')->toArray();
        $rejectedIds = $sentRequests->where('status', 'rejected')->pluck('receiver_id')->toArray();

        // Nếu chưa tìm kiếm, chỉ trả về danh sách lời mời đã gửi
        if (!$query) {
            return view('friends.search', compact('pendingRequests', 'pendingIds', 'rejectedIds'))->with('users', null);
        }

        // Tìm kiếm tất cả người dùng khớp với từ khóa
        $users = User::where('id', '!=', $userId)
            ->where(function ($q) use ($query) {
                $q->where('first_name', 'LIKE', "%$query%")
                    ->orWhere('last_name', 'LIKE', "%$query%");
            })
            ->get();

        return view('friends.search', compact('users', 'pendingRequests', 'pendingIds', 'rejectedIds'));
    }


    //Hủy lời mời
    public function cancelRequest(Request $request)
    {
        $receiverId = $request->input('receiver_id');

        $friendRequest = FriendRequest::where('sender_id', Auth::id())
            ->where('receiver_id', $receiverId)
            ->where('status', 'pending')
            ->first();

        if (!$friendRequest) {
            return redirect()->back()->with('error', 'Không tìm thấy lời mời.');
        }

        $friendRequest->delete();

        return redirect()->back()->with('success', 'Lời mời đã được hủy.');
    }

}
