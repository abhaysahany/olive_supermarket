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
            'message' => 'Welcome to Admin Dashboard'
        ]);
    }

    public function allUsers()
    {
        $users = User::all();
        return response()->json([
            'users' => $users
        ]);
    }
}
