<?php


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\TrendingNewsController;
use App\Http\Controllers\AboutSectionController;


use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;




Route::post('/news', [NewsController::class, 'store']);
Route::get('/news', [NewsController::class, 'index']);
Route::get('/news/{id}', [NewsController::class, 'show']);
Route::patch('/news/{id}', [NewsController::class, 'update']);
Route::delete('/news/{id}', [NewsController::class, 'destroy']);
Route::get('/categories', [CategoryController::class, 'index']);
Route::post('/categories', [CategoryController::class, 'store']);
Route::get('/news-titles', [NewsController::class, 'getNewsTitles']);

Route::get('/trending-news', [TrendingNewsController::class, 'index']);
Route::post('/trending-news', [TrendingNewsController::class, 'store']);
    
Route::post('/upload-image', [NewsController::class, 'uploadImage']);
Route::get('/tinker', [NewsController::class, 'getAllTitlesInTinker']);

// Route::get('/about', [AboutSectionController::class, 'show']);
// Route::post('/about', [AboutSectionController::class, 'update']);

Route::get('/users', [AuthController::class, 'getAllUsers']);
Route::delete('/users/{id}', [AuthController::class, 'deleteUser']);

Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

Route::middleware('auth:api')->group(function () {
    Route::get('me', [AuthController::class, 'me']);
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('refresh', [AuthController::class, 'refresh']);
    
Route::get('/users', [AuthController::class, 'getAllUsers']);
Route::delete('/users/{id}', [AuthController::class, 'deleteUser']);

});

Route::get('storage/{path}', function ($path) {
    $filePath = storage_path('app/public/' . $path);
    
    if (!File::exists($filePath)) {
        abort(404);
    }
    
    $file = File::get($filePath);
    $type = File::mimeType($filePath);
    
    return Response::make($file, 200)->header("Content-Type", $type);
})->where('path', '.*');






