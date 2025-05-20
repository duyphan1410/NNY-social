<?php

namespace App\Http\Controllers\Post\UserSearchController;

use App\Http\Controllers\Controller;  // Thêm Controller base class
use Illuminate\Http\Request;
use App\Models\User;

class UserSearchController extends Controller
{
    public function search(Request $request)
    {
        $query = $request->input('q'); // Lấy từ khóa tìm kiếm từ tham số 'q'

        if (empty($query)) {
            return response()->json([]); // Trả về mảng rỗng nếu không có từ khóa
        }

        // Tìm kiếm người dùng theo tên hoặc email
        $users = User::where(function ($q) use ($query) {
            $q->where('name', 'LIKE', '%' . $query . '%')
                ->orWhere('email', 'LIKE', '%' . $query . '%');
        })
            ->when(auth()->check(), function ($q) {
                // Lọc người dùng theo mối quan hệ, có thể điều chỉnh nếu cần
                // Ví dụ: Lọc theo bạn bè nếu cần
                // return $q->whereIn('id', auth()->user()->friends()->pluck('id'));
                return $q; // Không lọc nếu chưa có logic bạn bè
            })
            ->limit(10) // Giới hạn số lượng kết quả trả về
            ->get(['id', 'name', 'avatar']); // Lấy các cột cần thiết

        // Định dạng lại dữ liệu, thêm URL avatar
        $formattedUsers = $users->map(function($user) {
            return [
                'id' => $user->id,
                'name' => $user->name,
                'avatar_url' => $user->avatar ? asset('storage/' . $user->avatar) : asset('images/default_avatar.png'),
            ];
        });

        return response()->json($formattedUsers);
    }
}
