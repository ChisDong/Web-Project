<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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



Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// API GET IN WEBSITE
// Public API: list products for a category
Route::get('/categories/{category}/products', [App\Http\Controllers\ProductionsController::class, 'index_category'])->name('categories.products');

// Public API: list products for a collection
Route::get('/collections/{collection}/products', [App\Http\Controllers\ProductionsController::class, 'index_collection'])->name('collections.products');

// Public API: list products by it's colors
Route::get('/products-by-color/{id}', [App\Http\Controllers\ProductionsController::class, 'get_products_by_color'])->name('products.by.color');

// Public API: get reviews for a product
Route::get('/products-reviews/{id}', [App\Http\Controllers\ProductionsController::class, 'get_reviews'])->name('products.reviews');

// Public API: get highlights for a product
Route::get('/products-highlights/{id}', [App\Http\Controllers\ProductionsController::class, 'get_highlights'])->name('products.highlights');

// Public API: get faqs for a product
Route::get('/products-faqs/{id}', [App\Http\Controllers\ProductionsController::class, 'get_faqs'])->name('products.faqs');

// Public API: get images for a product
Route::get('/products-images/{id}', [App\Http\Controllers\ProductionsController::class, 'get_images'])->name('products.images');