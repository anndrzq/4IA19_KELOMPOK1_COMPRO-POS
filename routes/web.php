<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Dashboard\CategoryController;
use App\Http\Controllers\Dashboard\UnitController;
use App\Http\Controllers\Dashboard\SuplierController;
use App\Http\Controllers\Dashboard\UsersDataController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Authenticate

// Login
Route::controller(LoginController::class)->middleware('guest')->group(function () {
    Route::get('/', 'index')->name('login');
    Route::post('/', 'authenticate')->name('loginPost');
});
// Logout
Route::post('/Logout', [LoginController::class, 'logout'])->middleware('auth')->name('logout');

//Dashboard Data

// Data Master
// Suplier
Route::resource('/Suplier', SuplierController::class)->middleware('auth');
// Unit
Route::resource('/Unit', UnitController::class)->middleware('auth');
// Category
Route::resource('/Category', CategoryController::class)->middleware('auth');


// Setting Sections
// User Data
Route::resource('/UserData', UsersDataController::class)->middleware('auth');
