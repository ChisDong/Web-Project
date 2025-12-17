<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Termwind\Components\Raw;

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

//API Register
// xử lý "message": "CSRF token mismatch.",
// Public API: user registration
Route::middleware('web')->post('/register', [App\Http\Controllers\RegisterController::class, 'postRegister'])->name('api.register');
// Public API: user login
Route::middleware('web')->post('/login', [App\Http\Controllers\RegisterController::class, 'postLogin'])->name('api.login');
// Public API: user logout
Route::middleware('web')->post('/logout', [App\Http\Controllers\RegisterController::class, 'logout'])->name('api.logout');

// API IN WEBSITE
// Public API: list products for a category
Route::get('/categories/{category}/products', [App\Http\Controllers\ProductionsController::class, 'index_category'])->name('categories.products');
// Public API: list products for a collection
Route::get('/collections/{collection}/products', [App\Http\Controllers\ProductionsController::class, 'index_collection'])->name('collections.products');
// Public API: list products by it's colors
Route::get('/products-by-color/{product_id}', [App\Http\Controllers\ProductionsController::class, 'get_products_by_color'])->name('products.by.color');
// Public API: get reviews for a product
Route::get('/products-reviews/{product_id}', [App\Http\Controllers\ProductionsController::class, 'get_reviews'])->name('products.reviews');
// Public API: get highlights for a product
Route::get('/products-highlights/{product_id}', [App\Http\Controllers\ProductionsController::class, 'get_highlights'])->name('products.highlights');
// Public API: get faqs for a product
Route::get('/products-faqs/{product_id}', [App\Http\Controllers\ProductionsController::class, 'get_faqs'])->name('products.faqs');
// Public API: get images for a product
Route::get('/products-images/{product_id}', [App\Http\Controllers\ProductionsController::class, 'get_images'])->name('products.images');
// Public API: get products by name search
Route::get('/products-search', [App\Http\Controllers\ProductionsController::class, 'search_by_name'])->name('products.search');
// Public API: get product variants by product ID
Route::get('/product-variants/{product_id}', [App\Http\Controllers\ProductionsController::class, 'getProductVariants'])->name('products.variants');
//API FOR BUYING PROCESS
// Public API: add item to cart
Route::post('/orders/add-to-cart', [App\Http\Controllers\OrderController::class, 'addToCart'])->name('orders.addToCart');
// Public API update to cart order
Route::put('/orders/update-cart', [App\Http\Controllers\OrderController::class, 'updateCartItem'])->name('orders.updateCart');
//public API remove item from cart
Route::delete('/orders/remove-from-cart/{order_item_id}', [App\Http\Controllers\OrderController::class, 'removeCartItem'])->name('orders.removeFromCart');
//Public API apply address to order
Route::post('/orders/apply-address/{order_id}', [App\Http\Controllers\OrderController::class, 'applyAddress'])->name('orders.applyAddress');
//Public API: get cart Items 
Route::get('/cart/{user_id}', [App\Http\Controllers\OrderController::class, 'getCartItems'])->name('customer.cart.getItems');

// Public API: place an
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

//API FOR CUSTOMER
//Public API: get all orders for a user
Route::get('/orders/{user_id}', [App\Http\Controllers\CustomerController::class, 'getOrder'])->name('customer.orders.get');
//Public API: get order details by order ID
Route::get('/orders/detail/{order_id}', [App\Http\Controllers\CustomerController::class, 'getOrderById'])->name('customer.orders.getById');
//Public API: cancel an order
Route::post('/orders/cancel/{order_id}', [App\Http\Controllers\CustomerController::class, 'cancelOrder'])->name('customer.orders.cancel');
//Public API: get orders by status
Route::get('/orders/status/{status}', [App\Http\Controllers\CustomerController::class, 'getOrdersByStatus'])->name('customer.orders.status');
//Public API: get all items not be reviewed 
Route::get('/orders/review-reminder/{user_id}', [App\Http\Controllers\CustomerController::class, 'sendReviewReminder'])->name('customer.orders.reviewReminder');
//Public API: review order_items
Route::post('/orders/review/{variant_id}', [App\Http\Controllers\CustomerController::class, 'reviewOrderItem'])->name('customer.orders.review');
//Public API: get all addresses for a user
Route::get('/addresses/{user_id}', [App\Http\Controllers\CustomerController::class, 'getAddresses'])->name('customer.addresses.get');
//Public API: add a new address for a user
Route::post('/addresses', [App\Http\Controllers\CustomerController::class, 'postAddress'])->name('customer.addresses.add');
//Public API: update an address for a user
Route::put('/addresses/{address_id}', [App\Http\Controllers\CustomerController::class, 'updateAddress'])->name('customer.addresses.update');
//Public API: delete an address for a user
Route::delete('/addresses/{address_id}', [App\Http\Controllers\CustomerController::class, 'deleteAddress'])->name('customer.addresses.delete');
//Public API: set an address as default
Route::post('/addresses/default/{user_id}/{address_id}', [App\Http\Controllers\CustomerController::class, 'setDefaultAddress'])->name('customer.addresses.setDefault');
//Public API: get default address for a user
Route::get('/addresses/default/{user_id}', [App\Http\Controllers\CustomerController::class, 'getDefaultAddress'])->name('customer.addresses.getDefault');
//Public API: get my reviews
Route::get('/reviews/{user_id}', [App\Http\Controllers\CustomerController::class, 'getMyReviews'])->name('customer.reviews.get');
//Public API: delete my review
Route::delete('/reviews/{review_id}', [App\Http\Controllers\CustomerController::class, 'deleteReview'])->name('customer.reviews.delete');
//Public API: get notifications
Route::get('/notifications/{user_id}', [App\Http\Controllers\CustomerController::class, 'getNotifications'])->name('customer.notifications.get');
//Public API: mark all notification as read
Route::post('/notifications/read_all/{user_id}', [App\Http\Controllers\CustomerController::class, 'markAllNotificationsAsRead'])->name('customer.notifications.read');
//Public API: mark a notification as read
Route::post('/notifications/read/{notification_id}', [App\Http\Controllers\CustomerController::class, 'markNotificationAsRead'])->name('customer.notifications.readOne');
//Public API clear read notifications
Route::delete('/notifications/clear_read/{notification_id}', [App\Http\Controllers\CustomerController::class, 'clearReadNotifications'])->name('customer.notifications.clearRead');
//Public API: clear all notifications
Route::delete('/notifications/clear_all/{user_id}', [App\Http\Controllers\CustomerController::class, 'clearAllNotifications'])->name('customer.notifications.clearAll');