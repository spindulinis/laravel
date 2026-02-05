<?php

use App\Http\Controllers\AttributeController;
use App\Http\Controllers\AuthenticationController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PublicProductController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('authentication/sign-up', [AuthenticationController::class, 'signUp']);
Route::post('authentication/sign-in', [AuthenticationController::class, 'signIn']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('category/csv', [CategoryController::class, 'csv']);
    Route::resource('category', CategoryController::class);
    Route::patch('category/{firstCategory}/change-order/{secondCategory}', [CategoryController::class, 'changeOrder']);
    Route::resource('product', ProductController::class);
    Route::resource('user', UserController::class);
    Route::resource('attribute', AttributeController::class);
});

Route::resource('public-product', PublicProductController::class)->parameters([
    'public-product' => 'product'
]);
