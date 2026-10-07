<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CategoryController extends Controller
{
    public function index()
    {
        return response()->json(Category::with('subcategories')->get(), 200);
    }

    public function store(Request $request)
    {
        if ($request->has('name')) {
            $request->merge(['name' => trim($request->input('name'))]);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
            'description' => 'nullable|string',
            'image' => 'nullable|string'
        ]);

        try {
            $category = Category::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Category created successfully',
                'data' => $category
            ], 201);
        } catch (\Exception $e) {
            Log::error('Category creation failed: ' . $e->getMessage(), [
                'request' => $request->all(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Category creation failed',
                'data' => null
            ], 500);
        }
    }

    public function show(Category $category)
    {
        return response()->json($category->load('subcategories'), 200);
    }

    public function update(Request $request, Category $category)
    {
        if ($request->has('name')) {
            $request->merge(['name' => trim($request->input('name'))]);
        }

        $validated = $request->validate([
            'name' => [
                'sometimes',
                'string',
                'max:255',
                \Illuminate\Validation\Rule::unique('categories', 'name')->ignore($category->id),
            ],
            'description' => 'sometimes|nullable|string',
            'image' => 'sometimes|nullable|string'
        ]);

        try {
            $category->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Category updated successfully',
                'data' => $category->fresh()
            ], 200);
        } catch (\Exception $e) {
            Log::error('Category update failed: ' . $e->getMessage(), [
                'category_id' => $category->id,
                'request' => $request->all(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Category update failed',
                'data' => null
            ], 500);
        }
    }

    public function destroy(Category $category)
    {
        try {
            $category->delete();

            return response()->json([
                'success' => true,
                'message' => 'Category deleted successfully',
                'data' => null
            ], 200);
        } catch (\Exception $e) {
            Log::error('Category deletion failed: ' . $e->getMessage(), [
                'category_id' => $category->id,
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Category deletion failed',
                'data' => null
            ], 500);
        }
    }
}
