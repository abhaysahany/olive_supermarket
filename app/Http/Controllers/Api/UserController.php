<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function profile(Request $request)
    {
        $user = $request->user();

        if (! $user) {
            return response()->json([
                'message' => 'Unauthenticated.'
            ], 401);
        }

        $totalOrdersCount = $user->orders()->count();
        $recentOrders = $user->orders()
            ->with(['items.product', 'delivery'])
            ->latest()
            ->take(5)
            ->get();

        return response()->json([
            'user' => $user,
            'total_orders_count' => $totalOrdersCount,
            'recent_orders' => $recentOrders,
        ]);
    }
}
