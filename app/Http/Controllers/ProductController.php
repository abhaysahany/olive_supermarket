<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display a listing of products with filters.
     */
    public function index(Request $request)
    {
<<<<<<< HEAD
        $query = Product::with('subcategory.category');

        // Subcategory filter
        if ($request->filled('subcategory_id')) {
            $query->where('subcategory_id', $request->query('subcategory_id'));
        }

        // Category filter
        if ($request->filled('category_id')) {
            $query->whereHas('subcategory', function ($q) use ($request) {
                $q->where('category_id', $request->query('category_id'));
            });
        }

        // Search by keyword, brand, or SKU/UPC
        if ($request->filled('search')) {
            $search = $request->query('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('brand', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%")
                  ->orWhere('upc_barcode', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Brand filter
        if ($request->filled('brand')) {
            $query->where('brand', $request->query('brand'));
        }

        // Dietary / Supermarket flags
        if ($request->has('is_organic')) {
            $query->where('is_organic', filter_var($request->query('is_organic'), FILTER_VALIDATE_BOOLEAN));
        }

        if ($request->has('is_gluten_free')) {
            $query->where('is_gluten_free', filter_var($request->query('is_gluten_free'), FILTER_VALIDATE_BOOLEAN));
        }

        if ($request->has('is_perishable')) {
            $query->where('is_perishable', filter_var($request->query('is_perishable'), FILTER_VALIDATE_BOOLEAN));
        }

        // Price range filters
        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->query('min_price'));
        }

        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->query('max_price'));
        }

        // Status filter (defaults to active unless specified by admin)
        if ($request->filled('status')) {
            $query->where('status', $request->query('status'));
        } else {
            $query->where('status', 'active');
        }

        // Sorting
        $sortBy = $request->query('sort_by', 'latest');
        match ($sortBy) {
            'price_asc' => $query->orderBy('price', 'asc'),
            'price_desc' => $query->orderBy('price', 'desc'),
            'name_asc' => $query->orderBy('name', 'asc'),
            'name_desc' => $query->orderBy('name', 'desc'),
            default => $query->latest(),
        };

        return response()->json($query->paginate($request->query('per_page', 20)), 200);
=======
        return response()->json(Product::with(['category', 'subCategory'])->get(), 200);
>>>>>>> c657a9f198e4bd84e229150af1299d70dfb7de9f
    }

    /**
     * Store a newly created product (Admin only).
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
<<<<<<< HEAD
            'subcategory_id' => 'required|exists:subcategories,id',
            'sku' => 'required|string|max:50|unique:products,sku',
            'upc_barcode' => 'nullable|string|max:20',
            'name' => 'required|string|max:255',
            'brand' => 'nullable|string|max:100',
            'description' => 'nullable|string',
            'unit_size' => 'nullable|string|max:50',
            'price' => 'required|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0',
            'cost_price' => 'nullable|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'low_stock_threshold' => 'nullable|integer|min:0',
            'image' => 'nullable|string',
            'gallery_images' => 'nullable|array',
            'gallery_images.*' => 'string',
            'nutrition_facts' => 'nullable|array',
            'ingredients' => 'nullable|string',
            'is_organic' => 'nullable|boolean',
            'is_gluten_free' => 'nullable|boolean',
            'is_perishable' => 'nullable|boolean',
            'status' => 'nullable|in:active,inactive,out_of_stock'
=======
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
>>>>>>> c657a9f198e4bd84e229150af1299d70dfb7de9f
        ]);

        $product = Product::create($validated);
        return response()->json(['message' => 'Product created successfully', 'data' => $product->load('subcategory.category')], 201);
    }

    /**
     * Display a specific product.
     */
    public function show(Product $product)
    {
<<<<<<< HEAD
        return response()->json($product->load('subcategory.category'), 200);
=======
        return response()->json($product->load(['category', 'subCategory']), 200);
>>>>>>> c657a9f198e4bd84e229150af1299d70dfb7de9f
    }

    /**
     * Update a product (Admin only).
     */
    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
<<<<<<< HEAD
            'subcategory_id' => 'sometimes|exists:subcategories,id',
            'sku' => 'sometimes|string|max:50|unique:products,sku,' . $product->id,
            'upc_barcode' => 'nullable|string|max:20',
            'name' => 'sometimes|string|max:255',
            'brand' => 'nullable|string|max:100',
            'description' => 'nullable|string',
            'unit_size' => 'nullable|string|max:50',
            'price' => 'sometimes|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0',
            'cost_price' => 'nullable|numeric|min:0',
            'stock' => 'sometimes|integer|min:0',
            'low_stock_threshold' => 'nullable|integer|min:0',
            'image' => 'nullable|string',
            'gallery_images' => 'nullable|array',
            'gallery_images.*' => 'string',
            'nutrition_facts' => 'nullable|array',
            'ingredients' => 'nullable|string',
            'is_organic' => 'nullable|boolean',
            'is_gluten_free' => 'nullable|boolean',
            'is_perishable' => 'nullable|boolean',
            'status' => 'nullable|in:active,inactive,out_of_stock'
=======
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
>>>>>>> c657a9f198e4bd84e229150af1299d70dfb7de9f
        ]);

        $product->update($validated);
        return response()->json(['message' => 'Product updated successfully', 'data' => $product->load('subcategory.category')], 200);
    }

    /**
     * Remove a product (Admin only).
     */
    public function destroy(Product $product)
    {
        $product->delete();
        return response()->json(['message' => 'Product deleted successfully'], 200);
    }
}
