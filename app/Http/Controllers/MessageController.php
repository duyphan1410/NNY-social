<?php

namespace App\Http\Controllers;

use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Events\MessageSent;

class MessageController extends Controller
{
    public function index($userId)
    {
        $authId = Auth::id();

        $messages = Message::where(function($q) use ($authId, $userId) {
            $q->where('sender_id', $authId)
                ->where('receiver_id', $userId);
        })
            ->orWhere(function($q) use ($authId, $userId) {
                $q->where('sender_id', $userId)
                    ->where('receiver_id', $authId);
            })
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json($messages);
    }


    public function send(Request $request)
    {
        $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'content'     => 'required|string'
        ]);

        $message = Message::create([
            'sender_id'   => Auth::id(),
            'receiver_id' => $request->input('receiver_id'),
            'content'     => $request->input('content'),
        ]);
        // Fire the event - QUAN TRỌNG!
        broadcast(new MessageSent($message))->toOthers();

        return response()->json($message->load('sender', 'receiver'));
    }

    public function markRead($userId)
    {
        Message::where('receiver_id', Auth::id())
            ->where('sender_id', $userId)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return response()->json(['status' => 'ok']);
    }

    public function destroy($id)
    {
        $message = Message::findOrFail($id);

        // Chỉ cho phép người gửi xóa
        if ($message->sender_id !== Auth::id()) {
            return response()->json(['error' => 'Không có quyền xóa'], 403);
        }

        $message->delete();

        return response()->json(['success' => true]);
    }

}
