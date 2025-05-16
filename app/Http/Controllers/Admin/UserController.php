<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $users = User::query()
            ->when($request->search, function ($q) use ($request) {
                $q->where(function ($query) use ($request) {
                    $query->where('first_name', 'like', '%' . $request->search . '%')
                        ->orWhere('last_name', 'like', '%' . $request->search . '%')
                        ->orWhere('username', 'like', '%' . $request->search . '%');
                });
            })
            ->paginate(10);

        return view('admin.users.index', compact('users'));
    }

    public function toggleBan($id)
    {
        $user = User::findOrFail($id);

        if ($user->is_admin) {
            return back()->with('error', 'Không thể khóa admin khác.');
        }

        $user->banned = !$user->banned;
        $user->save();

        return back()->with('success', 'Cập nhật trạng thái người dùng thành công.');
    }
}
