<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    // User endpoints
    public function index(Request $request)
    {
        $orders = $request->user()
            ->orders()
            ->with('items.product')
            ->latest()
            ->get();

        return response()->json($orders, 200);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'integer', 'distinct', 'exists:products,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
        ]);

        try {
            $order = DB::transaction(function () use ($request, $validated) {
                $totalPrice = 0;
                $order = Order::create([
                    'user_id' => $request->user()->id,
                    'total_price' => 0,
                    'status' => 'pending',
                ]);

                $orderItems = [];

                foreach ($validated['items'] as $item) {
                    $product = Product::whereKey($item['product_id'])
                        ->lockForUpdate()
                        ->firstOrFail();

                    $quantity = (int) $item['quantity'];

                    if ($product->stock < $quantity) {
                        throw new \Exception("Not enough stock for {$product->name}");
                    }

                    $itemPrice = $product->price;
                    $totalPrice += $itemPrice * $quantity;

                    $orderItems[] = [
                        'order_id' => $order->id,
                        'product_id' => $product->id,
                        'quantity' => $quantity,
                        'price' => $itemPrice,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];

                    $product->stock -= $quantity;
                    $product->save();
                }

                if (!empty($orderItems)) {
                    $order->items()->createMany($orderItems);
                }

                $order->update(['total_price' => $totalPrice]);

                return $order->load('items.product');
            });

            return response()->json([
                'message' => 'Order placed successfully',
                'order' => $order,
            ], 201);
        } catch (\Throwable $e) {
            Log::error('Unable to place order', [
                'user_id' => $request->user()->id,
                'exception' => $e,
            ]);

            return response()->json(['error' => 'Unable to place order. ' . $e->getMessage()], 400);
        }
    }

    public function show(Request $request, Order $order)
    {
        $user = $request->user();

        if (! $user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        if ((int) $order->user_id !== (int) $user->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return response()->json($order->load('items.product'), 200);
    }

    // Admin endpoints
    public function adminIndex(Request $request)
    {
        $user = $request->user();

        if (! $user || ! $user->is_admin) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $orders = Order::with('user', 'items.product')->latest()->get();

        return response()->json($orders, 200);
    }

    public function updateStatus(Request $request, Order $order)
    {
        $user = $request->user();

        if (! $user || ! $user->is_admin) {
            Log::error('Forbidden attempt to update order status', [
                'user_id' => $user?->id,
                'order_id' => $order->id,
                'requested_status' => $request->input('status'),
            ]);

            return response()->json(['message' => 'Forbidden'], 403);
        }

        $validated = $request->validate([
            'status' => ['required', 'string', 'in:pending,processing,completed,cancelled'],
        ]);

        $currentStatus = $order->status;
        $allowedTransitions = [
            'pending' => ['processing', 'cancelled'],
            'processing' => ['completed', 'cancelled'],
            'completed' => [],
            'cancelled' => [],
        ];

        if ($currentStatus === $validated['status']) {
            return response()->json([
                'message' => 'Order status is already set to ' . $validated['status'],
                'order' => $order->fresh(),
            ], 200);
        }

        if (isset($allowedTransitions[$currentStatus]) && ! in_array($validated['status'], $allowedTransitions[$currentStatus], true)) {
            Log::error('Invalid order status transition attempted', [
                'user_id' => $user->id,
                'order_id' => $order->id,
                'current_status' => $currentStatus,
                'requested_status' => $validated['status'],
            ]);

            return response()->json([
                'message' => 'Invalid status transition from ' . $currentStatus . ' to ' . $validated['status'],
            ], 422);
        }

        $order->update(['status' => $validated['status']]);

        return response()->json(['message' => 'Order status updated', 'order' => $order->fresh()], 200);
    }
}
