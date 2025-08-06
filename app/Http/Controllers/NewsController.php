<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\News;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log; 
class NewsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
  public function index(Request $request)
{
    try {
        $perPage = $request->input('per_page', 10); // Default 10 items per page
        $perPage = min($perPage, 100); // Max 100 items per page
        
        $news = News::oldest()->paginate($perPage);
        
        return response()->json([
            'success' => true,
            'data' => $news->items(),
            'pagination' => [
                'current_page' => $news->currentPage(),
                'last_page' => $news->lastPage(),
                'per_page' => $news->perPage(),
                'total' => $news->total(),
                'from' => $news->firstItem(),
                'to' => $news->lastItem(),
                'has_more' => $news->hasMorePages()
            ]
        ], 200);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'error' => 'Failed to retrieve news items: ' . $e->getMessage()
        ], 500);
    }
}
    /**
     * Show the form for creating a new resource.
     */
   
    /**
     * Store a newly created resource in storage.
     */
public function store(Request $request)
{
    try {
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'link' => 'required|url|max:2000',
        ]);
        
        $news = News::create([
            'title' => $validatedData['title'],
            'link' => $validatedData['link'],
            'date' => now(),
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'News created successfully',
            'data' => $news
        ], 201);

    } catch (\Illuminate\Validation\ValidationException $e) {
        return response()->json([
            'success' => false,
            'errors' => $e->errors()
        ], 422);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'error' => 'Failed to create news item: ' . $e->getMessage()
        ], 500);
    }
}

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $news = News::findOrFail($id);
            return response()->json([
                'success' => true,
                'data' => $news
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'News item not found: ' . $e->getMessage()], 404);
        }
    }


    /**
     * Update the specified resource in storage.
     */
public function update(Request $request, string $id)
{
    try{
        $news = News::findOrFail($id);

        $validatedData = $request->validate([
            'title' => 'sometimes|string|max:255',
            'link' => 'sometimes|url|max:2000',  
        ]);

        if (empty($validatedData)) {
            return response()->json([
                'success' => false,
                'message' => 'No valid data provided for update'
            ], 400);
        }

        $news->update($validatedData);

        return response()->json([
            'success' => true,
            'message' => 'News item updated successfully',
            'data' => $news->fresh()
        ], 200);

    } catch (\Illuminate\Validation\ValidationException $e) {
        return response()->json([
            'success' => false,
            'errors' => $e->errors()
        ], 422);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'error' => 'Failed to edit news item: ' . $e->getMessage()
        ], 500);
    }
}
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
      try{
        $news = News::findOrFail($id);
        $news->delete();

        return response()->json([
            'success' => true,
            'message' => 'News item deleted successfully'
        ], 200);
      } catch (\Exception $e) {
        return response()->json(['error' => 'Failed to delete news item: ' . $e->getMessage()], 500);
      }

    }
}
