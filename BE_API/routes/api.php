<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\FileCategoryController;
use App\Http\Controllers\UniversityController;
use App\Http\Controllers\UserFileController;
use App\Models\User;
use App\Models\UserFile;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Public routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Authenticated routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', function (Request $request) {
        $request->user()->tokens()->delete();

        return response()->json(['message' => 'Logged out']);
    });
    Route::get('/profile', [AuthController::class, 'profile']);

    Route::get('/download/{path}', [UserFileController::class, 'download'])->where('path', '.*');

    // ✅ Only keep this /user route — remove the duplicate
    Route::get('/user', function (Request $request) {
        $user = $request->user()->loadMissing([
            'university:id,name',
            'files:id,user_id,file_category_id,file_url',
            'files.fileCategory:id,name,image_url', // Load category name for each file
            'photoFile:id,user_id,file_category_id,file_url',
            'photoFile.fileCategory:id,name', // Also load category for photo
        ]);

        // Format files
        $user->files = $user->files->map(function ($file) {
            return [
                'file_url' => $file->file_url,
                'url' => asset('storage/' . $file->file_url),
                'name' => $file->fileCategory->name ?? 'Unnamed File',
            ];
        });

        // Format photo file
        if ($user->photoFile) {
            $user->photo_file = [
                'file_category_id' => $user->photoFile->file_category_id,
                'file_url' => $user->photoFile->file_url,
                'url' => asset('storage/' . $user->photoFile->file_url),
                'name' => $user->photoFile->fileCategory->name ?? 'Photo',
            ];
            unset($user->photoFile);
        }

        return response()->json(['user' => $user]);
    });

    Route::get('/dashboard', fn(Request $request) => response()->json([
        'user' => $request->user()
    ]));

    // Admin routes
    Route::middleware('admin')->group(function () {
        Route::get('/admin/users', [AdminController::class, 'index']);  // List all users
        Route::get('/admin/users/all', [AdminController::class, 'all_user']); // List all users (you may want to differentiate this if needed)
        Route::get('/admin/users/{id}', [AdminController::class, 'show']);  // View details of a specific user by ID
        Route::post('/admin/users/{id}/approve', [AdminController::class, 'approve']);  // Approve a user
    });
});


//Upload function

Route::middleware('auth:sanctum')->post('user-files/upload', [UserFileController::class, 'upload']);

// Add these routes for managing file categories
Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('file-categories', FileCategoryController::class); // CRUD routes for categories
});

//Test

Route::middleware('auth:sanctum')->get('/test-user', function () {
    return App\Models\User::with('university', 'files')->find(auth()->id());
});

// Resources

//file-cats
Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('file-categories', FileCategoryController::class);
});

//univs
Route::apiResource('universities', UniversityController::class);

// Health check
Route::get('/ping', fn() => response()->json(['message' => 'pong']));
