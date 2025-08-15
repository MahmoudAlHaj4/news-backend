<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\News;
use Illuminate\Support\Facades\Http;
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
        $perPage = $request->input('per_page', 15); 
        $perPage = min($perPage, 100); 
$newsQuery = News::query();

if ($request->has('category_id') && !is_null($request->category_id)) {
    $newsQuery->where('category_id', $request->category_id);
}

   $news = $newsQuery->latest()
            ->select('id', 'title', 'main_image', 'category_id', 'priority', 'created_at', 'add_to_tinker')
            ->paginate($perPage);

        
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
   public function getNewsTitles(Request $request)
{
    try {
        $news = News::select( 'title', )
            ->get();

        return response()->json([
            'success' => true,
            'data' => $news
        ], 200);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'error' => 'Failed to retrieve news titles: ' . $e->getMessage()
        ], 500);
    }
}

// public function store(Request $request)
// {
//     try {
//         Log::info('Store News Request:', $request->all());

//         $validatedData = $request->validate([
//             'title' => 'required|string|max:255',
//             'main_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', 
//             'content' => 'required|string',
//             'category_id' => 'nullable|integer|exists:categories,id',
//             'published_at' => 'nullable|date',
//             'add_to_tinker' => 'nullable|boolean',
//             'priority' => 'nullable|in:عاجل,عادي',
//         ]);
//         if (!empty($validatedData['priority']) && $validatedData['priority'] === 'عاجل') {
//             $count = News::where('priority', 'عاجل')->count();
//             if ($count >= 5) {
//                 return response()->json([
//                     'success' => false,
//                     'error' => 'Maximum 5 عاجل news allowed'
//                 ], 422);
//             }
//         }


//         if ($request->hasFile('main_image')) {
//             $path = $request->file('main_image')->store('news_images', 'public');
//             $validatedData['main_image'] = $path;
//         }

//         if (empty($validatedData['category_id'])) {
//             $defaultCategory = \App\Models\Category::where('key', 'mid')->first();
//             $validatedData['category_id'] = $defaultCategory ? $defaultCategory->id : null;
//         }

//         $news = \App\Models\News::create($validatedData);

//         return response()->json([
//             'success' => true,
//             'message' => 'News created successfully',
//             'data' => $news,
//         ], 201);

//     } catch (\Illuminate\Validation\ValidationException $e) {
//         Log::warning('Validation Error:', $e->errors());
//         return response()->json([
//             'success' => false,
//             'errors' => $e->errors(),
//         ], 422);

//     } catch (\Exception $e) {
//         Log::error('News Store Error: ' . $e->getMessage(), ['exception' => $e]);
//         return response()->json([
//             'success' => false,
//             'error' => 'Failed to create news item: ' . $e->getMessage(),
//         ], 500);
//     }
// }

public function store(Request $request)
{
    try {
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'main_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
            'content' => 'required|string',
            'category_id' => 'nullable|integer|exists:categories,id',
            'published_at' => 'nullable|date',
            'add_to_tinker' => 'nullable|boolean',
            'priority' => 'nullable|in:عاجل,عادي',
        ]);

        // ✅ Limit "عاجل" news to 5
        if (!empty($validatedData['priority']) && $validatedData['priority'] === 'عاجل') {
            $count = News::where('priority', 'عاجل')->count();
            if ($count >= 5) {
                return response()->json([
                    'success' => false,
                    'error' => 'Maximum 5 عاجل news allowed'
                ], 422);
            }
        }

        // ✅ Upload to ImgBB
        if ($request->hasFile('main_image')) {
            $imageData = base64_encode(file_get_contents($request->file('main_image')->getRealPath()));
            $response = Http::asForm()->post('https://api.imgbb.com/1/upload', [
                'key' => env('IMGBB_API_KEY'),
                'image' => $imageData
            ]);

            if ($response->successful()) {
                // Save the full ImgBB link
                $validatedData['main_image'] = $response->json()['data']['url'];
            } else {
                return response()->json([
                    'success' => false,
                    'error' => 'Image upload failed: ' . $response->body()
                ], 500);
            }
        }

        // ✅ Default category if not provided
        if (empty($validatedData['category_id'])) {
            $defaultCategory = \App\Models\Category::where('key', 'mid')->first();
            $validatedData['category_id'] = $defaultCategory ? $defaultCategory->id : null;
        }

        // ✅ Save news
        $news = \App\Models\News::create($validatedData);

        return response()->json([
            'success' => true,
            'message' => 'News created successfully',
            'data' => $news
        ], 201);

    } catch (\Illuminate\Validation\ValidationException $e) {
        return response()->json([
            'success' => false,
            'errors' => $e->errors(),
        ], 422);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'error' => 'Failed to create news item: ' . $e->getMessage(),
        ], 500);
    }
}




// public function uploadImage(Request $request)
// {
//     try {
//         $request->validate([
//             'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:5120', // 5MB
//         ]);

//         $path = $request->file('image')->store('news_images', 'public');
//         $url = asset('storage/' . $path);

//         return response()->json([
//             'success' => true,
//             'url' => $url
//         ]);
//     } catch (\Illuminate\Validation\ValidationException $e) {
//         return response()->json([
//             'success' => false,
//             'error' => $e->errors()
//         ], 422);
//     } catch (\Exception $e) {
//         return response()->json([
//             'success' => false,
//             'error' => 'Failed to upload image: ' . $e->getMessage()
//         ], 500);
//     }
// }

public function uploadImage(Request $request)
{
    try {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:5120',
        ]);

        $imageData = base64_encode(file_get_contents($request->file('image')->getRealPath()));
        $response = Http::asForm()->post('https://api.imgbb.com/1/upload', [
            'key' => env('IMGBB_API_KEY'),
            'image' => $imageData
        ]);

        if ($response->successful()) {
            return response()->json([
                'success' => true,
                'url' => $response->json()['data']['url']
            ]);
        }

        return response()->json([
            'success' => false,
            'error' => 'Image upload failed'
        ], 500);

    } catch (\Illuminate\Validation\ValidationException $e) {
        return response()->json([
            'success' => false,
            'error' => $e->errors()
        ], 422);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'error' => 'Failed to upload image: ' . $e->getMessage()
        ], 500);
    }
}

public function getAllTitlesInTinker()
{
    $titles = News::where('add_to_tinker', 1)
        ->pluck('title')
        ->toArray();

    return response()->json([
        'titles' => $titles,
    ]);
}

    public function show(string $id)
    {
        try {
            // Log::info('News show start: ' . microtime(true));
            $news = News::findOrFail($id);
        // 

  
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
            'title' => 'required|string|max:255',
            'main_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', 
            'content' => 'required|string',
            'category_id' => 'nullable|integer|exists:categories,id',
            'published_at' => 'nullable|date',
            'add_to_tinker' => 'nullable|boolean',
            'priority' => 'nullable|in:عاجل,عادي',
        ]);

        if ($request->hasFile('main_image')) {
            $path = $request->file('main_image')->store('news_images', 'public');
            $validatedData['main_image'] = $path;
        }

        if (empty($validatedData['category_id'])) {
            $defaultCategory = \App\Models\Category::where('key', 'mid')->first();
            $validatedData['category_id'] = $defaultCategory ? $defaultCategory->id : null;
        }

        $news->update($validatedData);

        return response()->json([
            'success' => true,
            'message' => 'News item updated successfully',
            'data' => $news,
        ], 200);

    }catch (\Illuminate\Validation\ValidationException $e) {
        return response()->json([
            'success' => false,
            'errors' => $e->errors(),
        ], 422);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'error' => 'Failed to update news item: ' . $e->getMessage(),
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
