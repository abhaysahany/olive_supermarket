<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function index()
    {
        return response()->json([
            'data' => Product::with(['category', 'subCategory'])->latest()->get(),
        ], 200);
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'category_id' => ['required', 'integer', 'exists:categories,id'],
                'sub_category_id' => ['nullable', 'integer', 'exists:sub_categories,id'],
                'name' => ['required', 'string', 'max:255'],
                'slug' => ['nullable', 'string', 'max:255', 'unique:products,slug'],
                'description' => ['nullable', 'string'],
                'size' => ['nullable', 'string', 'max:255'],
                'short_size' => ['nullable', 'string', 'max:255'],
                'price' => ['required', 'numeric', 'min:0'],
                'old_price' => ['nullable', 'numeric', 'min:0'],
                'save_pct' => ['nullable', 'integer', 'min:0', 'max:100'],
                'stock' => ['required', 'integer', 'min:0'],
                'image' => ['nullable', 'string', 'max:2048'],
                'emoji' => ['nullable', 'string', 'max:20'],
                'tint' => ['nullable', 'string', 'max:50'],
                'tag' => ['nullable', 'string', 'max:255'],
                'rating' => ['nullable', 'numeric', 'min:0', 'max:5'],
                'reviews_count' => ['nullable', 'integer', 'min:0'],
                'local' => ['nullable', 'boolean'],
            ]);

            $validated['name'] = trim(strip_tags($validated['name']));
            $validated['description'] = isset($validated['description']) ? trim(strip_tags($validated['description'])) : null;
            $validated['slug'] = !empty($validated['slug']) ? Str::slug($validated['slug']) : Str::slug($validated['name']);

            $baseSlug = $validated['slug'];
            $slug = $baseSlug;
            $counter = 1;

            while (Product::where('slug', $slug)->exists()) {
                $slug = $baseSlug . '-' . $counter;
                $counter++;
            }

            $validated['slug'] = $slug;
            $validated['price'] = (float) $validated['price'];
            $validated['old_price'] = $validated['old_price'] !== null ? (float) $validated['old_price'] : null;
            $validated['stock'] = (int) $validated['stock'];

            $product = Product::create($validated);

            return response()->json(['message' => 'Product created', 'data' => $product], 201);
        } catch (\Throwable $e) {
            Log::error('Product creation failed', [
                'request_data' => $request->all(),
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            throw $e;
        }
    }

    public function show(Product $product)
    {
        return response()->json([
            'data' => $product->load(['category', 'subCategory']),
        ], 200);
    }

    public function update(Request $request, Product $product)
    {
        try {
            $validated = $request->validate([
                'category_id' => ['sometimes', 'integer', 'exists:categories,id'],
                'sub_category_id' => ['nullable', 'integer', 'exists:sub_categories,id'],
                'name' => ['sometimes', 'string', 'max:255'],
                'slug' => ['nullable', 'string', 'max:255', 'unique:products,slug,' . $product->id],
                'description' => ['nullable', 'string'],
                'size' => ['nullable', 'string', 'max:255'],
                'short_size' => ['nullable', 'string', 'max:255'],
                'price' => ['sometimes', 'numeric', 'min:0'],
                'old_price' => ['nullable', 'numeric', 'min:0'],
                'save_pct' => ['nullable', 'integer', 'min:0', 'max:100'],
                'stock' => ['sometimes', 'integer', 'min:0'],
                'image' => ['nullable', 'string', 'max:2048'],
                'emoji' => ['nullable', 'string', 'max:20'],
                'tint' => ['nullable', 'string', 'max:50'],
                'tag' => ['nullable', 'string', 'max:255'],
                'rating' => ['nullable', 'numeric', 'min:0', 'max:5'],
                'reviews_count' => ['nullable', 'integer', 'min:0'],
                'local' => ['nullable', 'boolean'],
            ]);

            if (isset($validated['name'])) {
                $validated['name'] = trim(strip_tags($validated['name']));
            }

            if (array_key_exists('description', $validated)) {
                $validated['description'] = $validated['description'] !== null ? trim(strip_tags($validated['description'])) : null;
            }

            if (isset($validated['slug']) && $validated['slug'] !== null && $validated['slug'] !== '') {
                $validated['slug'] = Str::slug($validated['slug']);
            } elseif (isset($validated['name'])) {
                $validated['slug'] = Str::slug($validated['name']);
            }

            if (isset($validated['slug'])) {
                $baseSlug = $validated['slug'];
                $slug = $baseSlug;
                $counter = 1;

                while (Product::where('slug', $slug)->whereKeyNot($product->id)->exists()) {
                    $slug = $baseSlug . '-' . $counter;
                    $counter++;
                }

                $validated['slug'] = $slug;
            }

            if (isset($validated['price'])) {
                $validated['price'] = (float) $validated['price'];
            }

            if (array_key_exists('old_price', $validated)) {
                $validated['old_price'] = $validated['old_price'] !== null ? (float) $validated['old_price'] : null;
            }

            if (isset($validated['stock'])) {
                $validated['stock'] = (int) $validated['stock'];
            }

            $product->fill($validated);
            $product->save();

            return response()->json([
                'message' => 'Product updated',
                'data' => $product->fresh()->load(['category', 'subCategory']),
            ], 200);
        } catch (\Throwable $e) {
            Log::error('Product update failed', [
                'product_id' => $product->id,
                'request_data' => $request->all(),
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            throw $e;
        }
    }

    public function destroy(Product $product)
    {
        if (!$product->delete()) {
            return response()->json(['message' => 'Product could not be deleted'], 500);
        }

        return response()->json(['message' => 'Product deleted'], 200);
    }
}
