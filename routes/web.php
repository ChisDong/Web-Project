<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductionsController;
use Termwind\Components\Raw;
use App\Models\User;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\CustomerController;
use PharIo\Version\SpecificMajorAndMinorVersionConstraint;

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

Route::get('/register', [RegisterController::class,  'showForm'])->name('showForm');
Route::post('/register', [RegisterController::class,  'postRegister'])->name('postRegister');
Route::get('/login', [RegisterController::class,  'showLogin'])->name('showLogin');
Route::post('/login', [RegisterController::class,  'postLogin'])->name('postLogin');
Route::middleware('auth')->get('/', function(){
    return view('welcome');
})->name('home');
Route::post('logout', [RegisterController::class,  'logout'])->name('logout');
Route::middleware('role')->group(function () {

    Route::get('/manager', [AdminController::class, 'showManager'])->name('showManager');
    Route::get('/admin/users', [AdminController::class, 'showIndex'])->name('showIndex');
    Route::delete('/admin/users/{id}', [AdminController::class, 'deleteUser'])->name('deleteUser');
});

// Profile routes (edit/update) for authenticated users
Route::middleware('auth')->group(function () {
    Route::get('/profile/edit', [CustomerController::class, 'edit'])->name('profile.edit');
    Route::post('/profile', [CustomerController::class, 'update'])->name('profile.update');
});
