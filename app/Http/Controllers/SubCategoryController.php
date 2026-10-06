<?php

namespace App\Http\Controllers;

use App\Models\SubCategory;
use Illuminate\Http\Request;

class SubCategoryController extends Controller
{
    public function index()
    {
        return response()->json(SubCategory::with('category')->get(), 200);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:sub_categories,slug',
            'description' => 'nullable|string'
        ]);

        $subCategory = SubCategory::create($validated);
        return response()->json(['message' => 'SubCategory created', 'data' => $subCategory], 201);
    }

    public function show(SubCategory $subCategory)
    {
        return response()->json($subCategory->load('category'), 200);
    }

    public function update(Request $request, SubCategory $subCategory)
    {
        $validated = $request->validate([
            'category_id' => 'sometimes|exists:categories,id',
            'name' => 'sometimes|string|max:255',
            'slug' => 'sometimes|string|max:255|unique:sub_categories,slug,' . $subCategory->id,
            'description' => 'nullable|string'
        ]);

        $subCategory->update($validated);
        return response()->json(['message' => 'SubCategory updated', 'data' => $subCategory], 200);
    }

    public function destroy(SubCategory $subCategory)
    {
        $subCategory->delete();
        return response()->json(['message' => 'SubCategory deleted'], 200);
    }
}
