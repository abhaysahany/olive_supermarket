<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    // User endpoints
    public function index(Request $request)
    {
        $orders = $request->user()->orders()->with('items.product')->get();
        return response()->json($orders, 200);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'items' => 'required|array',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1'
        ]);

        DB::beginTransaction();
        try {
            $totalPrice = 0;
            $order = Order::create([
                'user_id' => $request->user()->id,
                'total_price' => 0, // Will update shortly
                'status' => 'pending'
            ]);

            foreach ($validated['items'] as $item) {
                $product = Product::findOrFail($item['product_id']);
                
                if ($product->stock < $item['quantity']) {
                    throw new \Exception("Not enough stock for {$product->name}");
                }

                $itemPrice = $product->price;
                $totalPrice += $itemPrice * $item['quantity'];

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'quantity' => $item['quantity'],
                    'price' => $itemPrice
                ]);

                // Reduce stock
                $product->decrement('stock', $item['quantity']);
            }

            $order->update(['total_price' => $totalPrice]);
            DB::commit();

            return response()->json(['message' => 'Order placed successfully', 'order' => $order->load('items')], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function show(Request $request, Order $order)
    {
        if ($order->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        return response()->json($order->load('items.product'), 200);
    }

    // Admin endpoints
    public function adminIndex()
    {
        $orders = Order::with('user', 'items.product')->get();
        return response()->json($orders, 200);
    }

    public function updateStatus(Request $request, Order $order)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,processing,completed,cancelled'
        ]);

        $order->update($validated);
        return response()->json(['message' => 'Order status updated', 'order' => $order], 200);
    }
}
