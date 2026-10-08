<?php

namespace App\Http\Controllers;

use App\Models\SubCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class SubCategoryController extends Controller
{
    public function index()
    {
        return response()->json([
            'data' => SubCategory::with('category')->orderBy('name')->get(),
        ], 200);
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'category_id' => 'required|exists:categories,id',
                'name' => 'required|string|max:255',
                'slug' => 'nullable|string|max:255|unique:sub_categories,slug',
                'description' => 'nullable|string'
            ]);

            $validated['slug'] = $validated['slug'] ?? Str::slug($validated['name']);

            $subCategory = SubCategory::create($validated);
            return response()->json(['message' => 'SubCategory created', 'data' => $subCategory], 201);
        } catch (\Throwable $e) {
            Log::error('SubCategory creation failed', [
                'request' => $request->all(),
                'error' => $e->getMessage(),
            ]);

            return response()->json(['message' => 'SubCategory creation failed: ' . $e->getMessage()], 500);
        }
    }

    public function show($id)
    {
        $subCategory = $id instanceof SubCategory ? $id : SubCategory::find($id);

        if (!$subCategory) {
            return response()->json([
                'success' => false,
                'message' => "This Subcategory does not exist."
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $subCategory->load('category')
        ], 200);
    }

    public function update(Request $request, $id)
    {
        $subCategory = $id instanceof SubCategory ? $id : SubCategory::find($id);

        if (!$subCategory) {
            return response()->json([
                'success' => false,
                'message' => "This Subcategory does not exist."
            ], 404);
        }

        try {
            $validated = $request->validate([
                'category_id' => 'sometimes|exists:categories,id',
                'name' => 'sometimes|string|max:255',
                'slug' => 'sometimes|string|max:255|unique:sub_categories,slug,' . $subCategory->id,
                'description' => 'nullable|string'
            ]);

            if (isset($validated['name']) && empty($validated['slug'] ?? '')) {
                $validated['slug'] = Str::slug($validated['name']);
            }

            $subCategory->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'SubCategory updated successfully',
                'data' => $subCategory
            ], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $e->errors()
            ], 422);
        } catch (\Throwable $e) {
            Log::error('SubCategory update failed', [
                'sub_category_id' => $subCategory->id,
                'request' => $request->all(),
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'SubCategory update failed: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        $subCategory = $id instanceof SubCategory ? $id : SubCategory::find($id);

        if (!$subCategory) {
            return response()->json([
                'success' => false,
                'message' => "This Subcategory does not exist."
            ], 404);
        }

        try {
            $subCategory->delete();
            return response()->json([
                'success' => true,
                'message' => 'SubCategory deleted successfully',
                'data' => $subCategory
            ], 200);
        } catch (\Throwable $e) {
            Log::error('SubCategory delete failed', [
                'sub_category_id' => $subCategory->id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'SubCategory delete failed: ' . $e->getMessage()
            ], 500);
        }
    }
}
