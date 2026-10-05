<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        return response()->json(Product::with(['category', 'subCategory'])->get(), 200);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'sub_category_id' => 'nullable|exists:sub_categories,id',
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|unique:products,slug',
            'description' => 'nullable|string',
            'size' => 'nullable|string',
            'short_size' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'old_price' => 'nullable|numeric|min:0',
            'save_pct' => 'nullable|integer',
            'stock' => 'required|integer|min:0',
            'image' => 'nullable|string',
            'emoji' => 'nullable|string',
            'tint' => 'nullable|string',
            'tag' => 'nullable|string',
            'rating' => 'nullable|numeric',
            'reviews_count' => 'nullable|integer',
            'local' => 'boolean'
        ]);

        $product = Product::create($validated);
        return response()->json(['message' => 'Product created', 'data' => $product], 201);
    }

    public function show(Product $product)
    {
        return response()->json($product->load(['category', 'subCategory']), 200);
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'category_id' => 'sometimes|exists:categories,id',
            'sub_category_id' => 'nullable|exists:sub_categories,id',
            'name' => 'sometimes|string|max:255',
            'slug' => 'nullable|string|unique:products,slug,' . $product->id,
            'description' => 'nullable|string',
            'size' => 'nullable|string',
            'short_size' => 'nullable|string',
            'price' => 'sometimes|numeric|min:0',
            'old_price' => 'nullable|numeric|min:0',
            'save_pct' => 'nullable|integer',
            'stock' => 'sometimes|integer|min:0',
            'image' => 'nullable|string',
            'emoji' => 'nullable|string',
            'tint' => 'nullable|string',
            'tag' => 'nullable|string',
            'rating' => 'nullable|numeric',
            'reviews_count' => 'nullable|integer',
            'local' => 'boolean'
        ]);

        $product->update($validated);
        return response()->json(['message' => 'Product updated', 'data' => $product], 200);
    }

    public function destroy(Product $product)
    {
        $product->delete();
        return response()->json(['message' => 'Product deleted'], 200);
    }
}
