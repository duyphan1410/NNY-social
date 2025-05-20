<?php

namespace App\Http\Controllers\UserSearchController;

use App\Http\Controllers\Controller;  // Thêm Controller base class
use Illuminate\Http\Request;
use App\Models\User;

class UserSearchController extends Controller
{
    public function search(Request $request)
    {
        $query = $request->input('q');

        if (empty($query)) {
            return response()->json([]);
        }


        $users = User::where('first_name', 'like', "%{$query}%")
            ->limit(10)
            ->get();


        $formattedUsers = $users->map(function ($user) {
            return [
                'id' => $user->id,
                'name' => $user->first_name . ' ' . $user->last_name,
                'avatar_url' => $user->avatar,
            ];
        });

        return response()->json($formattedUsers);
    }

}
