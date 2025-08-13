<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
      public function index(Request $request)
    {
        try {
            $categories = Category::all();
            
            return response()->json([
                'success' => true,
                'data' => $categories
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Failed to retrieve categories: ' . $e->getMessage()
            ], 500);
        }
    
    }
    public function store(Request $request)
    {
        $request->validate([
            'key' => 'required|string|max:255|unique:categories',
            'display_name' => 'required|string|max:255',
        ]);

        try {
            $category = Category::create($request->all());

            return response()->json([
                'success' => true,
                'data' => $category
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Failed to create category: ' . $e->getMessage()
            ], 500);
        }
    }
}
