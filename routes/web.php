<?php

use App\Models\StockIn;
use App\Models\Discount;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Dashboard\UnitController;
use App\Http\Controllers\dashboard\CashierController;
use App\Http\Controllers\Dashboard\MembersController;
use App\Http\Controllers\Dashboard\ProductController;
use App\Http\Controllers\Dashboard\StockInController;
use App\Http\Controllers\Dashboard\SuplierController;
use App\Http\Controllers\Dashboard\CategoryController;
use App\Http\Controllers\dashboard\RefundsControllers;
use App\Http\Controllers\Dashboard\StockOutController;
use App\Http\Controllers\Dashboard\UsersDataController;
use App\Http\Controllers\dashboard\salesHistoryController;
use App\Http\Controllers\Dashboard\DashboardAdminController;
use App\Http\Controllers\Landing\indexController;

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
//Landing Page
Route::get('/', [indexController::class, 'index']);

// Authenticate
// Login
Route::controller(LoginController::class)->middleware('guest')->group(function () {
    Route::get('/auth', 'index')->name('login');
    Route::post('/auth', 'authenticate')->name('loginPost');
});
// Logout
Route::post('/Logout', [LoginController::class, 'logout'])->middleware('auth')->name('logout');

//Dashboard Data
Route::get('/Dashboard', [DashboardAdminController::class, 'index'])->middleware('auth')->name('Dashboard.index');

// Data Master
// Suplier
Route::resource('/Suplier', SuplierController::class)->middleware('auth');
// Unit
Route::resource('/Unit', UnitController::class)->middleware('auth');
// Category
Route::resource('/Category', CategoryController::class)->middleware('auth');
// Product
Route::resource('/Product', ProductController::class)->middleware('auth');
// Discount
// Route::resource('/Discount', DiscountController::class)->middleware('auth');

//Cashier
Route::middleware('auth')->group(function () {
    Route::get('/cashier', [CashierController::class, 'index'])->name('cashier');
    Route::post('/cashier', [CashierController::class, 'store'])->name('cashier.store');
});

// Report
// StockIn
Route::resource('/StockIn', StockInController::class)->middleware('auth');
Route::resource('/StockOut', StockOutController::class)->middleware('auth');
Route::resource('/SalesHistory', salesHistoryController::class)->middleware('auth');
Route::post('/refunds', [RefundsControllers::class, 'store'])->name('refunds.store');

// Setting Sections
// User Data
Route::resource('/UserData', UsersDataController::class)->middleware('auth');
// Members
Route::resource('/Member', MembersController::class)->middleware('auth');
