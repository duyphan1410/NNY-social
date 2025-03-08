<?php

namespace App\Http\Controllers\Reel;

use Illuminate\Http\Request;
use App\Models\Reel;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;

class ReelController extends Controller
{
    public function create()
    {
        return view('reel.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'media' => 'required|file|mimes:jpg,jpeg,png,mp4|max:20480', // 20MB max
            'caption' => 'nullable|string|max:255',
            'duration' => 'nullable|integer|min:1',
            'status' => 'nullable|string|in:active,draft,hidden',
            'is_public' => 'nullable|boolean',
            'audio_id' => 'nullable|integer|exists:audios,id', // Nếu có bảng âm thanh
        ]);

        $path = $request->file('media')->store('reel', 'public');


        return redirect()->route('home')->with('success', 'Tin đã được tạo!');
    }
}
