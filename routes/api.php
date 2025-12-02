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
// Public API: get products by name search
Route::get('/products-search', [App\Http\Controllers\ProductionsController::class, 'search_by_name'])->name('products.search');

// API POST FOR ADMIN DASHBOARD
Route::post('/categories', [App\Http\Controllers\CategoryCollectionController::class, 'postCategory'])->name('categories.post');
// Public API: create a new collection
Route::post('/collections', [App\Http\Controllers\CategoryCollectionController::class, 'postCollection'])->name('collections.post');
// public API: create a new product
Route::post('/products', [App\Http\Controllers\ProductionsController::class, 'postProduct'])->name('products.post');
// Public API: create a new product image
Route::post('/product-images', [App\Http\Controllers\ProductionsController::class, 'postProductImage'])->name('product.images.post');
// Public API: create a new product discount
Route::post('/product-discounts', [App\Http\Controllers\ProductionsController::class, 'postDicount'])->name('product.discounts.post');
// Public API: create a new product color
Route::post('/product-colors', [App\Http\Controllers\ProductionsController::class, 'postProductColor'])->name('product.colors.post');
// Public API: create a new product FAQ
Route::post('/product-faqs', [App\Http\Controllers\ProductionsController::class, 'postProductFaq'])->name('product.faqs.post');
// Public API: create a new product highlight
Route::post('/product-highlights', [App\Http\Controllers\ProductionsController::class, 'postHighLight'])->name('product.highlights.post');
// Protected API: create a new product (admin only)
//API DELETE FOR ADMIN DASHBOARD

//API FOR CUSTOMER ORDER
