<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\FileCategoryController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserFileController;
use Illuminate\Support\Facades\Route;
use App\Models\User;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/users', function () {
    return User::all();
});

Route::middleware(['auth', 'is_admin'])->group(function () {
    Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
});

// Route::middleware(['auth:sanctum'])->group(function () {
//     Route::get('/download/{path}', [UserFileController::class, 'download'])
//         ->where('path', '.*');
// });

Route::prefix('admin')->middleware('auth', 'admin')->group(function () {
    Route::get('file-categories/create', [FileCategoryController::class, 'create'])->name('admin.file-category.create');
    Route::post('file-categories', [FileCategoryController::class, 'store'])->name('admin.file-category.store');
});

Route::get('/download/{path}', [UserFileController::class, 'download'])
    ->where('path', '.*');
