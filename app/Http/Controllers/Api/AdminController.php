<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class AdminController extends Controller
{
    public function dashboard()
    {
        return response()->json([
            'status' => 'success',
            'message' => 'Welcome to Admin Dashboard'
        ], 200);
    }

    public function allUsers()
    {
        $users = User::all();

        return response()->json([
            'status' => 'success',
            'users' => $users,
            'count' => $users->count()
        ], 200);
    }
}
