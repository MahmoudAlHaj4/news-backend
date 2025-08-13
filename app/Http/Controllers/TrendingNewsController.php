<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TrendingNews;

class TrendingNewsController extends Controller
{
    public function index()
    {
           
     
    }

    public function store(Request $request)
    {
        try{
            $validatedData = $request->validate([
                'title'=> 'required|string|max:255',
                'link' => 'required|string|max:200',
                'image' => 'required|string|max:200',

            ]);
            $trendingNews = TrendingNews::create($validatedData);
            return response()->json(['message' => 'Trending news created successfully.', 'data' => $trendingNews], 201);

        }catch(\Exception $e){
            return response()->json(['error' => 'An error occurred while storing the trending news.'], 500);
        }
    }
}
