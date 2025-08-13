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
}
